<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Token;

use Illuminate\Foundation\Http\FormRequest;

class FlushTokensRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [];
    }

    public function handle()
    {
        $this->user()->tokens()->delete();

        return $this->getResponse();
    }

    public function getResponse()
    {
        $message = __('All tokens have been revoked.');

        return $this->wantsJson()
            ? response()->json(['success' => true, 'message' => $message])
            : redirect()->back()->with('status', $message);
    }
}
