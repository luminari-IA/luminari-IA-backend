<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class LlmCredentialService
{
    /**
     * Obtiene la clave de Groq a utilizar. Puede ser BYOK (Bring Your Own Key) del usuario o la global del sistema.
     */
    public function getApiKey(User $user): string
    {
        // En una implementación real de BYOK:
        // if ($user->groq_api_key) {
        //     return Crypt::decryptString($user->groq_api_key);
        // }

        return env('GROQ_API_KEY', '');
    }
}
