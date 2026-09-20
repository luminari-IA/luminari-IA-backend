<?php

namespace App\Services;

use App\Models\User;

class UsageMeteringService
{
    /**
     * Verifica si el usuario tiene saldo suficiente de tokens o consultas.
     */
    public function canMakeRequest(User $user, int $estimatedTokens = 100): bool
    {
        if ($user->role === 'admin' || $user->role === 'teacher') {
            return true;
        }

        $plan = $user->plan;

        if (!$plan) {
            return false; // No tiene plan, no puede consultar
        }

        if ($plan->tokens_limit === 0) {
            return true; // Plan ilimitado
        }

        return ($user->tokens_used + $estimatedTokens) <= $plan->tokens_limit;
    }

    /**
     * Registra el uso de tokens.
     */
    public function recordUsage(User $user, int $tokensUsed)
    {
        if ($user->role === 'admin' || $user->role === 'teacher') {
            return;
        }

        $user->tokens_used += $tokensUsed;
        $user->save();
    }
}
