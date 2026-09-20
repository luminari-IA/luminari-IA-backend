<?php

namespace App\Services;

use App\Models\TutorSession;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Exception;

class TutorAgentService
{
    protected LlmCredentialService $credentialService;

    public function __construct(LlmCredentialService $credentialService)
    {
        $this->credentialService = $credentialService;
    }

    /**
     * Envía un mensaje a Groq (Nexa) y obtiene la respuesta.
     */
    public function sendMessage(User $user, TutorSession $session, string $message): string
    {
        // Guardar mensaje del usuario
        $session->messages()->create([
            'role' => 'user',
            'content' => $message,
        ]);

        // Construir el historial para la API
        $messagesPayload = [
            [
                'role' => 'system',
                'content' => "Eres Nexa, una tutora virtual amigable, empática y experta, diseñada para la plataforma educativa Luminary. Siempre respondes en español. El estudiante quiere aprender sobre la materia: " . ($session->subject->name ?? 'General') . ". Explica los conceptos paso a paso de manera clara y didáctica."
            ]
        ];

        // Añadir contexto previo (últimos 10 mensajes)
        $history = $session->messages()->orderBy('created_at', 'asc')->take(10)->get();
        foreach ($history as $msg) {
            $messagesPayload[] = [
                'role' => $msg->role,
                'content' => $msg->content,
            ];
        }

        $apiKey = $this->credentialService->getApiKey($user);

        if (empty($apiKey)) {
            throw new Exception("La clave API de Groq no está configurada.");
        }

        // Llamada a la API de Groq
        $response = Http::withToken($apiKey)
            ->timeout(60)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama3-8b-8192', // Modelo por defecto de Groq
                'messages' => $messagesPayload,
                'temperature' => 0.7,
                'max_tokens' => 1024,
            ]);

        if ($response->failed()) {
            throw new Exception("Error al contactar a Nexa (Groq): " . $response->body());
        }

        $data = $response->json();
        $replyContent = $data['choices'][0]['message']['content'] ?? 'No pude generar una respuesta.';

        // Guardar respuesta del asistente
        $session->messages()->create([
            'role' => 'assistant',
            'content' => $replyContent,
        ]);

        return $replyContent;
    }
}
