<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/subjects",
     *     tags={"Materias"},
     *     summary="Listar todas las materias activas",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Lista de materias")
     * )
     */
    public function index()
    {
        $subjects = Subject::where('is_active', true)->get();
        return response()->json(['data' => $subjects]);
    }

    /**
     * @OA\Get(
     *     path="/api/subjects/{id}",
     *     tags={"Materias"},
     *     summary="Obtener detalle de una materia",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *     @OA\Response(response="200", description="Detalle de la materia")
     * )
     */
    public function show($id)
    {
        $subject = Subject::findOrFail($id);
        return response()->json(['data' => $subject]);
    }
}
