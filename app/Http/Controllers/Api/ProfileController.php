<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo_path' => $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : null,
            'theme_color' => $user->theme_color,
            'font_family' => $user->font_family,
            'border_style' => $user->border_style,
            'reduced_animations' => (bool)$user->reduced_animations,
            'tts_speed' => $user->tts_speed,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'theme_color' => 'sometimes|string|max:20',
            'font_family' => 'sometimes|string|max:50',
            'border_style' => 'sometimes|in:rounded,smooth,square',
            'reduced_animations' => 'sometimes|boolean',
            'tts_speed' => 'sometimes|in:slow,normal,fast',
            'photo' => 'nullable|image|max:2048' // max 2MB
        ]);

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }
        if (isset($validated['theme_color'])) {
            $user->theme_color = $validated['theme_color'];
        }
        if (isset($validated['font_family'])) {
            $user->font_family = $validated['font_family'];
        }
        if (isset($validated['border_style'])) {
            $user->border_style = $validated['border_style'];
        }
        if (isset($validated['reduced_animations'])) {
            $user->reduced_animations = $validated['reduced_animations'];
        }
        if (isset($validated['tts_speed'])) {
            $user->tts_speed = $validated['tts_speed'];
        }

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            $path = $request->file('photo')->store('profiles', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo_path' => $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : null,
                'theme_color' => $user->theme_color,
                'font_family' => $user->font_family,
                'border_style' => $user->border_style,
                'reduced_animations' => (bool)$user->reduced_animations,
                'tts_speed' => $user->tts_speed,
            ]
        ]);
    }
}
