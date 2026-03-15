<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKvkDetailsRequest extends FormRequest
{
    /**
     * Bepaal of de gebruiker geautoriseerd is om dit verzoek te doen.
     * Alleen admins mogen KVK gegevens wijzigen.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('admin');
    }

    /**
     * Validatieregels voor het bijwerken van KVK gegevens.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kvk_naam' => ['nullable', 'string', 'max:100'],
            'kvk_nummer' => ['nullable', 'string', 'max:20'],
            'btw_nummer' => ['nullable', 'string', 'max:20'],
            'rsin' => ['nullable', 'string', 'max:20'],
            'vestigingsnummer' => ['nullable', 'string', 'max:20'],
            'nvwa_nummer' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Custom attribute namen voor foutmeldingen.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'kvk_naam' => 'KVK naam',
            'kvk_nummer' => 'KVK nummer',
            'btw_nummer' => 'BTW nummer',
            'rsin' => 'RSIN',
            'vestigingsnummer' => 'vestigingsnummer',
            'nvwa_nummer' => 'NVWA nummer',
        ];
    }
}
