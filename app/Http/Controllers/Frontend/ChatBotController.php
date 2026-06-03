<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($endpoint, [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful and friendly medical assistant. Answer general health and clinic-related questions clearly and politely. Do not provide diagnoses or offer medical advice as a substitute for consulting a licensed medical professional.',
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
