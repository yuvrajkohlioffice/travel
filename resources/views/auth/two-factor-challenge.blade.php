<x-guest-layout>
    <!-- Full page wrapper with background -->
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4 sm:px-6 lg:px-8">

        <!-- Card container -->
        <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 transition-all duration-300">

            <!-- Logo -->
            <div class="flex justify-center">
                <x-authentication-card-logo class="h-16 w-auto" />
            </div>

            <!-- Page heading -->
            <h2 class="mt-6 text-center text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                Two-Factor Authentication
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300" x-data="{ recovery: false }" x-show="! recovery">
                {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
            </p>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300" x-cloak x-show="recovery">
                {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
            </p>

            <!-- Validation Errors -->
            <x-validation-errors class="mt-4" />

            <!-- 2FA Form -->
            <form class="mt-6 space-y-6" method="POST" action="{{ route('two-factor.login') }}" x-data="{ recovery: false }">
                @csrf

                <!-- Authentication Code Input -->
                <div x-show="! recovery" class="transition-all duration-300">
                    <x-label for="code" value="{{ __('Authentication Code') }}" class="sr-only" />
                    <x-input
                        id="code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        autofocus
                        autocomplete="one-time-code"
                        placeholder="Enter authentication code"
                        x-ref="code"
                        class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all"
                    />
                </div>

                <!-- Recovery Code Input -->
                <div x-cloak x-show="recovery" class="transition-all duration-300">
                    <x-label for="recovery_code" value="{{ __('Recovery Code') }}" class="sr-only" />
                    <x-input
                        id="recovery_code"
                        name="recovery_code"
                        type="text"
                        autocomplete="one-time-code"
                        placeholder="Enter recovery code"
                        x-ref="recovery_code"
                        class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all"
                    />
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end mt-4 space-x-3">
                    <!-- Toggle between code/recovery -->
                    <button type="button"
                        class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 underline transition-colors"
                        x-show="! recovery"
                        x-on:click="
                            recovery = true;
                            $nextTick(() => { $refs.recovery_code.focus() })
                        ">
                        {{ __('Use a recovery code') }}
                    </button>

                    <button type="button"
                        class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 underline transition-colors"
                        x-cloak
                        x-show="recovery"
                        x-on:click="
                            recovery = false;
                            $nextTick(() => { $refs.code.focus() })
                        ">
                        {{ __('Use an authentication code') }}
                    </button>

                    <!-- Submit Button -->
                    <x-button class="ml-auto group relative flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        Log in
                    </x-button>
                </div>

                <!-- Livewire Loading State Placeholder -->
                <div wire:loading wire:target="twoFactorLogin" class="mt-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                    Verifying...
                </div>

                <!-- Livewire Error State Placeholder -->
                <div wire:loading.remove wire:target="twoFactorLogin">
                    @error('code')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                    @error('recovery_code')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                </div>
            </form>

        </div>
    </div>
</x-guest-layout>
