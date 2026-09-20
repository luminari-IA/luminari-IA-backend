<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/plans",
     *     tags={"Planes y Facturación"},
     *     summary="Listar todos los planes disponibles",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Lista de planes")
     * )
     */
    public function getPlans()
    {
        $plans = Plan::all();
        return response()->json(['data' => $plans]);
    }

    /**
     * @OA\Get(
     *     path="/api/my-plan",
     *     tags={"Planes y Facturación"},
     *     summary="Ver detalles de mi plan actual",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Detalle del plan del estudiante")
     * )
     */
    public function myPlan(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'plan' => $user->plan,
            'tokens_used' => $user->tokens_used,
            'role' => $user->role
        ]);
    }
}
