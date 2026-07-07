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

                return $this->localChat($request->input('message'));
            }

            $content = $response->json('choices.0.message.content');
            return response()->json(['message' => $content ?: 'Sorry, I could not generate a response.']);
        } catch (\Exception $exception) {
            Log::error('OpenAI chat exception', [
                'message' => $exception->getMessage(),
            ]);

            return $this->localChat($request->input('message'));
        }
    }

    public function triage(Request $request)
    {
        $request->validate([
            'symptoms' => 'required|string|max:1000',
        ]);

        $apiKey = config('services.openai.key');
        $endpoint = config('services.openai.endpoint');
        $model = config('services.openai.model', 'gpt-3.5-turbo');

        if (empty($apiKey)) {
            return response()->json([
                'message' => 'Chatbot is not configured. Please set OPENAI_API_KEY in your environment.',
            ], 500);
        }

        $specialties = Specialty::where('is_active', 1)->pluck('name')->toArray();
        if (empty($specialties)) {
             return response()->json(['specialty' => null]);
        }

        $specialtiesList = implode(', ', $specialties);

        $systemPrompt = "You are a professional clinic triager. Based on the patient's symptoms, you must match them to the single best fitting department/specialty from this list: [{$specialtiesList}].\n\n";
        $systemPrompt .= "Rules:\n";
        $systemPrompt .= "1. Reply ONLY with the exact specialty name from the list. Do not write introductory words, explanations, or punctuation.\n";
        $systemPrompt .= "2. If none of the specialties fit or symptoms are too vague, reply with 'None'.\n\n";
        $systemPrompt .= "Example responses: Cardiology, Pediatrics, None.";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($endpoint, [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => $request->input('symptoms'),
                    ],
                ],
                'temperature' => 0.1,
                'max_tokens' => 20,
            ]);

            if ($response->failed()) {
                Log::error('OpenAI triage request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return $this->localTriage($request->input('symptoms'));
            }

            $matched = trim($response->json('choices.0.message.content'));
            $matchedClean = str_replace(['.', '"', '\''], '', $matched);

            $found = null;
            foreach ($specialties as $spec) {
                if (strtolower($spec) === strtolower($matchedClean)) {
                    $found = Specialty::where('name', $spec)->first();
                    break;
                }
            }

            return response()->json([
                'specialty' => $found ? $found->name : null,
                'specialty_id' => $found ? $found->id : null,
            ]);
        } catch (\Exception $exception) {
            Log::error('OpenAI triage exception: ' . $exception->getMessage());
            return $this->localTriage($request->input('symptoms'));
        }
    }

    protected function localTriage(string $symptoms)
    {
        $specialties = Specialty::where('is_active', 1)->get();
        if ($specialties->isEmpty()) {
            return response()->json(['specialty' => null, 'specialty_id' => null]);
        }

        $symptomsLower = strtolower($symptoms);
        
        $matched = null;
        foreach ($specialties as $spec) {
            $name = strtolower($spec->name);
            if (str_contains($symptomsLower, $name)) {
                $matched = $spec;
                break;
            }
        }

        if (!$matched) {
            $mappings = [
                'cardio' => 'Cardiology',
                'heart' => 'Cardiology',
                'chest' => 'Cardiology',
                
                'child' => 'Pediatrics',
                'kid' => 'Pediatrics',
                'baby' => 'Pediatrics',
                'pediatric' => 'Pediatrics',
                
                'teeth' => 'Dental',
                'tooth' => 'Dental',
                'dentist' => 'Dental',
                
                'skin' => 'Dermatology',
                'rash' => 'Dermatology',
                'acne' => 'Dermatology',
            ];

            foreach ($mappings as $keyword => $specName) {
                if (str_contains($symptomsLower, $keyword)) {
                    $matched = Specialty::where('name', 'like', '%' . $specName . '%')->first();
                    if ($matched) {
                        break;
                    }
                }
            }
        }

        if (!$matched) {
            $matched = $specialties->first();
        }

        return response()->json([
            'specialty' => $matched->name,
            'specialty_id' => $matched->id,
            'is_fallback' => true
        ]);
    }

    protected function localChat(string $message)
    {
        $msgLower = strtolower($message);
        
        if (str_contains($msgLower, 'doctor') || str_contains($msgLower, 'physician')) {
            $doctors = Doctor::where('is_active', 1)->with('specialty')->get();
            $res = "Here are our active doctors:\n";
            foreach ($doctors as $d) {
                $res .= "- Dr. {$d->name} ({$d->specialty->name})\n";
            }
            return response()->json(['message' => $res]);
        }

        if (str_contains($msgLower, 'service') || str_contains($msgLower, 'offer')) {
            $services = Service::where('is_active', 1)->get();
            $res = "Here are our key services:\n";
            foreach ($services as $s) {
                $res .= "- {$s->name}\n";
            }
            return response()->json(['message' => $res]);
        }

        if (str_contains($msgLower, 'book') || str_contains($msgLower, 'appointment') || str_contains($msgLower, 'schedule')) {
            return response()->json([
                'message' => "To book, reschedule, or cancel appointments, please use the main navigation links or click the 'Book Appointment' button on the Doctors page."
            ]);
        }

        return response()->json([
            'message' => "Hello! I am the Arogya Clinic Assistant (running in offline local fallback mode). How can I help you today? You can ask about our doctors, services, or appointments."
        ]);
    }
}
