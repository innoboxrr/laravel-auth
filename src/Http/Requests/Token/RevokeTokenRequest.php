<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Token;

use Illuminate\Foundation\Http\FormRequest;

class RevokeTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'token_id' => ['required'],
        ];
    }

    /**
     * Sólo se revocan tokens propios, y `revoked` dice si se revocó alguno.
     * Antes respondía true siempre, aunque el id fuera de otro usuario y no se
     * borrara nada.
     */
    public function handle()
    {
        $revoked = $this->user()->tokens()->whereKey($this->input('token_id'))->delete() > 0;

        return $this->getResponse($revoked);
    }

    public function getResponse(bool $revoked = true)
    {
        return $this->wantsJson()
            ? response()->json(['revoked' => $revoked])
            : redirect(config('laravel-auth.routes.redirects.revoke-token'))->with('revoked', $revoked);
    }
}
