<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Subject;
use App\Models\LiveClass;
use App\Models\PromptTemplate;

class AdminController extends Controller
{
    // --- Subjects ---
    public function getSubjects() { return response()->json(['data' => Subject::all()]); }
    public function storeSubject(Request $request) {
        $validated = $request->validate(['name' => 'required', 'description' => 'nullable', 'is_active' => 'boolean']);
        $subject = Subject::create($validated);
        return response()->json(['data' => $subject]);
    }
    public function updateSubject(Request $request, $id) {
        $subject = Subject::findOrFail($id);
        $subject->update($request->all());
        return response()->json(['data' => $subject]);
    }
    public function destroySubject($id) {
        Subject::findOrFail($id)->delete();
        return response()->json(['message' => 'Eliminado']);
    }



    // --- Prompt ---
    public function getPrompt() { 
        $prompt = PromptTemplate::firstOrCreate(['name' => 'Nexa_System'], ['content' => 'Eres Nexa...']);
        return response()->json(['data' => $prompt]);
    }
    public function updatePrompt(Request $request) {
        $request->validate(['content' => 'required']);
        $prompt = PromptTemplate::firstOrCreate(['name' => 'Nexa_System']);
        $prompt->update(['content' => $request->content]);
        return response()->json(['data' => $prompt]);
    }
}
