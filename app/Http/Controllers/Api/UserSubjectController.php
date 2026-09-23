<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;

class UserSubjectController extends Controller
{
    // Obtener las materias inscritas del usuario actual
    public function index(Request $request)
    {
        $user = $request->user();
        $subjects = $user->subjects()->withPivot('level')->get();
        return response()->json(['data' => $subjects]);
    }

    // Obtener las materias disponibles (a las que NO está inscrito)
    public function available(Request $request)
    {
        $user = $request->user();
        $enrolledIds = $user->subjects()->pluck('subjects.id');
        $available = Subject::where('is_active', true)
                            ->whereNotIn('id', $enrolledIds)
                            ->get();
        return response()->json(['data' => $available]);
    }

    // Inscribir materias (desde onboarding o catálogo)
    public function store(Request $request)
    {
        $request->validate([
            'subjects' => 'required|array',
            'subjects.*.id' => 'required|exists:subjects,id',
            'subjects.*.level' => 'nullable|integer'
        ]);

        $user = $request->user();
        $syncData = [];

        foreach ($request->subjects as $subject) {
            $syncData[$subject['id']] = ['level' => $subject['level'] ?? 0];
        }

        // Sync without detaching para no borrar progreso si agregan desde el catálogo
        $user->subjects()->syncWithoutDetaching($syncData);

        return response()->json(['message' => 'Materias guardadas correctamente', 'data' => $user->subjects]);
    }
}
