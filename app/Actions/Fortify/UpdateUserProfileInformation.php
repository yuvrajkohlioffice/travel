<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        // Validate input, including optional WhatsApp API key
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'whatsapp_api_key' => ['nullable', 'string', 'max:255'], // new field
        ])->validateWithBag('updateProfileInformation');

        // Update profile photo if provided
        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        // Update verified user email if changed
        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            // Update user information including WhatsApp API key
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'whatsapp_api_key' => $input['whatsapp_api_key'] ?? $user->whatsapp_api_key,
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
            'whatsapp_api_key' => $input['whatsapp_api_key'] ?? $user->whatsapp_api_key,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
