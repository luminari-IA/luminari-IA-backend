<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TutorSession;
use App\Services\TutorAgentService;
use App\Services\UsageMeteringService;
use Illuminate\Http\Request;

class TutorController extends Controller
{
    protected TutorAgentService $tutorService;
    protected UsageMeteringService $usageService;

    public function __construct(TutorAgentService $tutorService, UsageMeteringService $usageService)
    {
        $this->tutorService = $tutorService;
        $this->usageService = $usageService;
    }

    /**
     * @OA\Post(
     *     path="/api/tutor/session",
     *     tags={"Tutor IA (Nexa)"},
     *     summary="Iniciar una nueva sesión de tutoría",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="subject_id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Dudas sobre fracciones")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Sesión creada")
     * )
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'nullable|string'
        ]);

        $session = $request->user()->tutorSessions()->create([
            'subject_id' => $request->subject_id,
            'title' => $request->title ?? 'Nueva sesión'
        ]);

        return response()->json(['data' => $session]);
    }

    /**
     * @OA\Post(
     *     path="/api/tutor/session/{id}/message",
     *     tags={"Tutor IA (Nexa)"},
     *     summary="Enviar un mensaje a Nexa en una sesión",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="¿Me explicas cómo sumar 1/2 y 1/4?")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Respuesta de Nexa")
     * )
     */
    public function sendMessage(Request $request, $sessionId)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $user = $request->user();
        $session = TutorSession::where('user_id', $user->id)->findOrFail($sessionId);

        // Validar tokens/saldo
        if (!$this->usageService->canMakeRequest($user, 150)) {
            return response()->json([
                'message' => 'Límite de tokens alcanzado. Por favor, actualiza tu plan.'
            ], 403);
        }

        try {
            $reply = $this->tutorService->sendMessage($user, $session, $request->message);
            
            // Registrar un estimado de 150 tokens gastados (puede ajustarse usando el usage de la API real)
            $this->usageService->recordUsage($user, 150);

            return response()->json(['reply' => $reply]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/tutor/session/{id}/history",
     *     tags={"Tutor IA (Nexa)"},
     *     summary="Obtener historial de chat de una sesión",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *     @OA\Response(response="200", description="Historial de mensajes")
     * )
     */
    public function getHistory(Request $request, $sessionId)
    {
        $session = TutorSession::where('user_id', $request->user()->id)->findOrFail($sessionId);
        return response()->json(['data' => $session->messages]);
    }
}
