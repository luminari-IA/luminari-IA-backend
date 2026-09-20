<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/evaluations",
     *     tags={"Evaluaciones"},
     *     summary="Obtener todas las evaluaciones del estudiante",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Lista de evaluaciones")
     * )
     */
    public function index(Request $request)
    {
        $evaluations = Evaluation::where('user_id', $request->user()->id)->with('subject')->get();
        return response()->json(['data' => $evaluations]);
    }

    /**
     * @OA\Post(
     *     path="/api/evaluations",
     *     tags={"Evaluaciones"},
     *     summary="Registrar una nueva evaluación",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="subject_id", type="integer", example=1),
     *             @OA\Property(property="score", type="number", example=9.5)
     *         )
     *     ),
     *     @OA\Response(response="200", description="Evaluación registrada")
     * )
     */
    public function store(Request $request)
    {
        // Solo para simular cuando el estudiante envía una evaluación
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'score' => 'required|numeric|min:0|max:10'
        ]);

        $evaluation = Evaluation::create([
            'user_id' => $request->user()->id,
            'subject_id' => $request->subject_id,
            'score' => $request->score,
            'feedback' => 'Evaluación registrada automáticamente.'
        ]);

        return response()->json(['data' => $evaluation]);
    }
}
