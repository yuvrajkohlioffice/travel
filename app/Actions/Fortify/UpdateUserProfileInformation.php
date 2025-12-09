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
        // Validate input including WhatsApp API key and company_id
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'whatsapp_api_key' => ['nullable', 'string', 'max:255'],
            'company_id' => ['nullable', 'exists:companies,id'], // new validation
        ])->validateWithBag('updateProfileInformation');

        // Update profile photo if provided
        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        // If email is changed and user must verify email
        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            // Regular update for non-verified or unchanged email
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'whatsapp_api_key' => $input['whatsapp_api_key'] ?? $user->whatsapp_api_key,
                'company_id' => $input['company_id'] ?? $user->company_id, // ensure company_id is updated
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
            'email_verified_at' => null, // reset verification
            'whatsapp_api_key' => $input['whatsapp_api_key'] ?? $user->whatsapp_api_key,
            'company_id' => $input['company_id'] ?? $user->company_id, // update company
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
