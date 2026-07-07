<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Specialty;
use App\Models\Doctor;
use App\Models\Service;

class ChatBotController extends Controller
{
    public function index()
    {
        return view('frontend.chatbot');
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $apiKey = config('services.openai.key');
        $endpoint = config('services.openai.endpoint');
        $model = config('services.openai.model', 'gpt-3.5-turbo');

        if (empty($apiKey)) {
            return response()->json([
                'message' => 'Chatbot is not configured. Please set OPENAI_API_KEY in your environment.',
            ], 500);
        }

        // Fetch active specialties, doctors with availabilities, and services
        $specialties = Specialty::where('is_active', 1)->pluck('name')->toArray();
        
        $doctors = Doctor::where('is_active', 1)
            ->with(['specialty', 'availabilities'])
            ->get();
            
        $doctorsList = [];
        $days = [
            0 => 'Sunday', 
            1 => 'Monday', 
            2 => 'Tuesday', 
            3 => 'Wednesday', 
            4 => 'Thursday', 
            5 => 'Friday', 
            6 => 'Saturday'
        ];
        foreach ($doctors as $doc) {
            $availText = [];
            foreach ($doc->availabilities as $av) {
                $dayName = $days[$av->day_of_week] ?? $av->day_of_week;
                $availText[] = "{$dayName}: " . date('h:i A', strtotime($av->start_time)) . " - " . date('h:i A', strtotime($av->end_time)) . " ({$av->slot_minutes} min slots)";
            }
            $availString = count($availText) ? implode(', ', $availText) : 'No scheduled hours';
            $specialtyName = $doc->specialty ? $doc->specialty->name : 'N/A';
            $doctorsList[] = "- Dr. {$doc->name} (Specialty: {$specialtyName}, Qualification: {$doc->qualification}, Bio: {$doc->bio}). Availabilities: {$availString}";
        }

        $services = Service::where('is_active', 1)->get();
        $servicesList = [];
        foreach ($services as $srv) {
            $servicesList[] = "- {$srv->name}: {$srv->description}";
        }

        $clinicContext = "You are a helpful and friendly medical assistant for our clinic. Answer general health and clinic-related questions clearly and politely. Do not provide diagnoses or offer medical advice as a substitute for consulting a licensed medical professional.\n\n";
        $clinicContext .= "Here is our real-time clinic catalog:\n\n";
        $clinicContext .= "SPECIALTIES:\n" . (count($specialties) ? "- " . implode("\n- ", $specialties) : "None") . "\n\n";
        $clinicContext .= "DOCTORS:\n" . (count($doctorsList) ? implode("\n", $doctorsList) : "No doctors listed") . "\n\n";
        $clinicContext .= "SERVICES:\n" . (count($servicesList) ? implode("\n", $servicesList) : "No services listed") . "\n\n";
        $clinicContext .= "If a patient asks about booking, rescheduling, or confirming an appointment, tell them to visit the 'Appointments' page on our website.";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($endpoint, [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $clinicContext,
                    ],
                    [
                        'role' => 'user',
                        'content' => $request->input('message'),
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 400,
            ]);

            if ($response->failed()) {
                Log::error('OpenAI chat request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                $errorMessage = $response->json('error.message') ?? 'Chatbot request failed. Please try again later.';
                return response()->json([
                    'message' => $errorMessage,
                ], $response->status() === 422 ? 422 : 500);
            }

            $content = $response->json('choices.0.message.content');
            return response()->json(['message' => $content ?: 'Sorry, I could not generate a response.']);
        } catch (\Exception $exception) {
            Log::error('OpenAI chat exception', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to process your request at the moment.',
            ], 500);
        }
    }
}
