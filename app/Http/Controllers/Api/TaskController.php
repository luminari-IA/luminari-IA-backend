<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     tags={"Tareas"},
     *     summary="Listar tareas del usuario",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Lista de tareas")
     * )
     */
    public function index(Request $request)
    {
        $tasks = Task::with('subject')->where('user_id', $request->user()->id)->get();
        return response()->json(['data' => $tasks]);
    }
}
