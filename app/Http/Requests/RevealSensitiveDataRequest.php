<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RevealSensitiveDataRequest extends FormRequest
{
    /**
     * Bepaal of de gebruiker geautoriseerd is om dit verzoek te doen.
     * Alleen admins mogen gevoelige gegevens inzien.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('admin');
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_uuid' => ['required', 'uuid', 'exists:users,uuid'],
            'field_type' => ['required', 'in:iban,bsn'],
        ];
    }
}
