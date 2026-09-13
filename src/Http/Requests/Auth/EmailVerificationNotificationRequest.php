<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class EmailVerificationNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [];
    }

    /**
     * `status` dice qué pasó: `verification-link-sent` o `already-verified`.
     * Antes `success` valía true en un caso y la cadena del estado en el otro.
     */
    public function getResponse(string $status)
    {
        return $this->wantsJson()
            ? response()->json(['success' => true, 'status' => $status])
            : redirect(config('laravel-auth.routes.redirects.email-verification-notification'));
    }

    public function handle()
    {
        if ($this->user()->hasVerifiedEmail()) {
            return $this->getResponse('already-verified');
        }

        $this->user()->sendEmailVerificationNotification();

        return $this->getResponse('verification-link-sent');
    }
}
