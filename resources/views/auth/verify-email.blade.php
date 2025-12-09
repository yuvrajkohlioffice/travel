<x-guest-layout>
    <!-- Full page wrapper with background -->
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4 sm:px-6 lg:px-8">
        
        <!-- Card container -->
        <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 transition-all duration-300">
            
            <!-- Logo -->
            <div class="flex justify-center">
                <x-authentication-card-logo class="h-16 w-auto" />
            </div>

            <!-- Heading -->
            <h2 class="mt-6 text-center text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                Verify Your Email
            </h2>

            <!-- Description -->
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">
                {{ __('Before continuing, please verify your email address by clicking the link we just emailed you. If you didn\'t receive the email, we can send you another.') }}
            </p>

            <!-- Success Status -->
            @if (session('status') == 'verification-link-sent')
                <div class="mt-4 p-3 text-green-700 bg-green-100 rounded-md text-sm text-center">
                    {{ __('A new verification link has been sent to your email address.') }}
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-6 flex flex-col sm:flex-row sm:justify-between items-center gap-3">

                <!-- Resend Verification Email -->
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-button class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white transition-all">
                        {{ __('Resend Verification Email') }}
                    </x-button>
                </form>

                <!-- Profile and Logout -->
                <div class="flex flex-col sm:flex-row items-center gap-2">
                    <a href="{{ route('profile.show') }}" class="underline text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                        {{ __('Edit Profile') }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="underline text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Livewire Loading State Placeholder -->
            <div wire:loading wire:target="resendVerification" class="mt-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                Sending verification email...
            </div>

            <!-- Livewire Error State Placeholder -->
            <div wire:loading.remove wire:target="resendVerification">
                @error('email')<p class="text-red-600 text-sm mt-2 text-center">{{ $message }}</p>@enderror
            </div>

        </div>
    </div>
</x-guest-layout>
