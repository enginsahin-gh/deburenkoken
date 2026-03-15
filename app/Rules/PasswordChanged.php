<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class PasswordChanged implements Rule
{
    private string $email;

    public function __construct(
        string $email
    ) {
        $this->email = $email;
    }

    public function passes($attribute, $value): bool
    {
        /** @var User $user */
        $user = User::where('email', $this->email)->first();

        return ! Hash::check($value, $user->getAuthPassword());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('passwords.same');
    }
}
