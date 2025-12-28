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
        Validator::make($input, [
            // Profile fields
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'whatsapp_api_key' => ['nullable', 'string', 'max:255'],
            'company_id' => ['nullable', 'exists:companies,id'],

            // ✅ SMTP validation
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer'],
            'smtp_encryption' => ['nullable', 'in:tls,ssl'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_from_email' => ['nullable', 'email', 'max:255'],
            'smtp_from_name' => ['nullable', 'string', 'max:255'],
        ])->validateWithBag('updateProfileInformation');

        // Update profile photo
        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        // Handle verified email change
        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
            return;
        }

        // ✅ Normal update
        $this->updateUser($user, $input);
    }

    /**
     * Update non-verified or unchanged email user
     */
    protected function updateUser(User $user, array $input): void
    {
        $user
            ->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'whatsapp_api_key' => $input['whatsapp_api_key'] ?? $user->whatsapp_api_key,
                'company_id' => $input['company_id'] ?? $user->company_id,

                // ✅ SMTP updates (only if provided)
                'smtp_host' => $input['smtp_host'] ?? $user->smtp_host,
                'smtp_port' => $input['smtp_port'] ?? $user->smtp_port,
                'smtp_encryption' => $input['smtp_encryption'] ?? $user->smtp_encryption,
                'smtp_username' => $input['smtp_username'] ?? $user->smtp_username,
                'smtp_from_email' => $input['smtp_from_email'] ?? $user->smtp_from_email,
                'smtp_from_name' => $input['smtp_from_name'] ?? $user->smtp_from_name,
            ])
            ->save();

        // 🔐 Update SMTP password only if provided
        if (!empty($input['smtp_password'])) {
            $user->smtp_password = $input['smtp_password']; // encrypted via mutator
            $user->save();
        }
    }

    /**
     * Update verified user's profile info
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user
            ->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'email_verified_at' => null,
                'whatsapp_api_key' => $input['whatsapp_api_key'] ?? $user->whatsapp_api_key,
                'company_id' => $input['company_id'] ?? $user->company_id,

                // ✅ SMTP
                'smtp_host' => $input['smtp_host'] ?? $user->smtp_host,
                'smtp_port' => $input['smtp_port'] ?? $user->smtp_port,
                'smtp_encryption' => $input['smtp_encryption'] ?? $user->smtp_encryption,
                'smtp_username' => $input['smtp_username'] ?? $user->smtp_username,
                'smtp_from_email' => $input['smtp_from_email'] ?? $user->smtp_from_email,
                'smtp_from_name' => $input['smtp_from_name'] ?? $user->smtp_from_name,
            ])
            ->save();

        if (!empty($input['smtp_password'])) {
            $user->smtp_password = $input['smtp_password'];
            $user->save();
        }

        $user->sendEmailVerificationNotification();
    }
}
