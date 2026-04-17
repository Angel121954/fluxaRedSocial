<?php

namespace App\Http\Responses;

use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse as FailedTwoFactorLoginResponseContract;

class FailedTwoFactorLoginResponse implements FailedTwoFactorLoginResponseContract
{
    public function toResponse($request)
    {
        $message = $request->filled('recovery_code')
            ? __('El código de recuperación proporcionado es inválido.')
            : __('El código de autenticación de dos factores proporcionado es inválido.');

        $key = $request->filled('recovery_code') ? 'recovery_code' : 'code';

        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                $key => [$message],
            ]);
        }

        return redirect()->route('two-factor.login')->withErrors([$key => $message]);
    }
}
