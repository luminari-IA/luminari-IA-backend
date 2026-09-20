<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Crypt;

class TripleEncryptCast implements CastsAttributes
{
    /**
     * Cast the given value (from database -> PHP).
     * Decrypt 3 times.
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        try {
            // Desencriptar 1
            $decrypted1 = Crypt::decryptString($value);
            // Desencriptar 2
            $decrypted2 = Crypt::decryptString($decrypted1);
            // Desencriptar 3
            $finalValue = Crypt::decryptString($decrypted2);
            
            return $finalValue;
        } catch (\Exception $e) {
            // Si el valor no estaba encriptado (ej: datos legacy), devolverlo tal cual.
            return $value;
        }
    }

    /**
     * Prepare the given value for storage (from PHP -> database).
     * Encrypt 3 times.
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        // Encriptar 1
        $encrypted1 = Crypt::encryptString($value);
        // Encriptar 2
        $encrypted2 = Crypt::encryptString($encrypted1);
        // Encriptar 3
        $finalValue = Crypt::encryptString($encrypted2);

        return $finalValue;
    }
}
