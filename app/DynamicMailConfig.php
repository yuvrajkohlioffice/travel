<?php

use App\Models\User;
use Illuminate\Support\Facades\Config;

if (!function_exists('setUserMailConfig')) {

    function setUserMailConfig(User $user): void
    {
        // ✅ If user SMTP not configured, fallback to .env
        if (
            empty($user->smtp_host) ||
            empty($user->smtp_port) ||
            empty($user->smtp_username) ||
            empty($user->smtp_password)
        ) {
            return;
        }

        // ✅ Force SMTP mailer
        Config::set('mail.default', 'smtp');

        // ✅ Override SMTP dynamically
        Config::set('mail.mailers.smtp', [
            'transport'  => 'smtp',
            'host'       => $user->smtp_host,
            'port'       => (int) $user->smtp_port,
            'encryption' => $user->smtp_encryption ?: null,
            'username'   => $user->smtp_username,
            'password'   => $user->smtp_password,
            'timeout'    => null,
            'auth_mode'  => null,
        ]);

        // ✅ From address
        Config::set('mail.from.address', $user->smtp_from_email ?? $user->email);
        Config::set('mail.from.name', $user->smtp_from_name ?? $user->name);
    }
}
