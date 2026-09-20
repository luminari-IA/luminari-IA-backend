<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/live-classes",
     *     tags={"Clases en Vivo"},
     *     summary="Listar clases en vivo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="subject_id", in="query", required=false, @OA\Schema(type="integer"), example=1),
     *     @OA\Response(response="200", description="Lista de clases en vivo")
     * )
     */
    public function index(Request $request)
    {
        $query = LiveClass::with('subject');
        
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $classes = $query->orderBy('scheduled_at', 'asc')->get();
        
        return response()->json(['data' => $classes]);
    }

    /**
     * @OA\Get(
     *     path="/api/live-classes/{id}",
     *     tags={"Clases en Vivo"},
     *     summary="Detalle de una clase en vivo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *     @OA\Response(response="200", description="Detalle de la clase")
     * )
     */
    public function show($id)
    {
        $liveClass = LiveClass::with('subject')->findOrFail($id);
        return response()->json(['data' => $liveClass]);
    }
}
