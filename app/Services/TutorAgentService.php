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
    public function sendMessage(User $user, TutorSession $session, string $message, ?string $image = null): string
    {
        // Guardar mensaje del usuario
        $session->messages()->create([
            'role' => 'user',
            'content' => $message,
        ]);

        $promptTemplate = \App\Models\PromptTemplate::where('name', 'Nexa_System')->first();
        $systemPrompt = $promptTemplate ? $promptTemplate->content : "Eres Nexa. El estudiante es {user_name} y la materia {subject_name}.";

        $subjectName = $session->subject->name ?? 'General';
        $userName = $user->name ?? 'Estudiante';
        $classTitle = $session->title ?? 'Clase';

        $systemPrompt = str_replace(
            ['{user_name}', '{subject_name}', '{class_title}'],
            [$userName, $subjectName, $classTitle],
            $systemPrompt
        );

        $systemPrompt .= "\n\nIMPORTANTE: No uses formato Markdown (como ##, *** o tablas) a menos que sea indispensable. Si haces listas, usa números o guiones simples. Responde siempre de forma conversacional y fácil de leer en voz alta.";
        $systemPrompt .= "\n\nSi el usuario adjunta una imagen a su mensaje, significa que te está mostrando su cámara web o pantalla compartida. Analiza la imagen detalladamente para responder su duda basándote en lo que ves en ella. NO le pidas que te lea la información si tú mismo puedes leerla en la imagen.";

        // Construir el historial para la API
        $messagesPayload = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ]
        ];

        // Añadir contexto previo
        $history = $session->messages()->orderBy('created_at', 'asc')->get();
        
        foreach ($history as $index => $msg) {
            $isLastMessage = ($index === count($history) - 1);
            
            // Si es el último mensaje y hay una imagen
            if ($isLastMessage && $msg->role === 'user' && $image) {
                $messagesPayload[] = [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $msg->content],
                        ['type' => 'image_url', 'image_url' => ['url' => $image]]
                    ]
                ];
            } else {
                $messagesPayload[] = [
                    'role' => $msg->role,
                    'content' => $msg->content,
                ];
            }
        }

        $apiKey = $this->credentialService->getApiKey($user);

        if (empty($apiKey)) {
            throw new Exception("La clave API de Groq no está configurada.");
        }

        // Determinar modelo
        $model = $image ? 'llama-3.2-11b-vision-preview' : 'openai/gpt-oss-20b';

        // Llamada a la API
        $response = Http::withToken($apiKey)
            ->timeout(60)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
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

    /**
     * Pide a Nexa que genere una tarea basada en la sesión.
     */
    public function generateTaskForSession(User $user, TutorSession $session): array
    {
        $subjectName = $session->subject->name ?? 'General';
        
        $systemPrompt = "Eres Nexa, la IA tutora. La clase de $subjectName acaba de terminar. Basándote en la conversación anterior (si la hubo) o en el tema en general, genera SÓLO una breve tarea o reto práctico de 1 párrafo para el estudiante.
NO uses formato Markdown. Responde en texto plano. No incluyas saludos ni despedidas, solo la descripción de la tarea.";

        $messagesPayload = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Añadir el contexto previo
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

        $response = Http::withToken($apiKey)
            ->timeout(60)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'openai/gpt-oss-20b',
                'messages' => $messagesPayload,
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

        if ($response->failed()) {
            throw new Exception("Error al contactar a Nexa (Groq): " . $response->body());
        }

        $data = $response->json();
        $taskDescription = $data['choices'][0]['message']['content'] ?? 'Escribe un resumen de lo que aprendiste hoy.';

        return [
            'title' => 'Reto de ' . $subjectName,
            'description' => trim($taskDescription)
        ];
    }
}
