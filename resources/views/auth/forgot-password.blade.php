<x-guest-layout>
    <!-- Page wrapper with background -->
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4 sm:px-6 lg:px-8">
        
        <!-- Card container -->
        <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 transition-all duration-300">
            
            <!-- Logo -->
            <div class="flex justify-center">
                <x-authentication-card-logo class="h-16 w-auto" />
            </div>

            <!-- Page heading -->
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                Forgot Your Password?
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">
                Enter your email and we will send you a password reset link.
            </p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mt-4 p-3 text-green-700 bg-green-100 rounded-md text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Validation Errors -->
            <x-validation-errors class="mt-4" />

            <!-- Forgot Password Form -->
            <form class="mt-8 space-y-6" method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Input -->
                <div>
                    <x-label for="email" value="{{ __('Email') }}" class="sr-only" />
                    <x-input
                        id="email"
                        name="email"
                        type="email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Email address"
                        class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm transition-all"
                    />
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-4">
                    <x-button class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        Email Password Reset Link
                    </x-button>
                </div>

                <!-- Livewire Loading State Placeholder -->
                <div wire:loading wire:target="sendResetLink" class="mt-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                    Sending password reset link...
                </div>

                <!-- Livewire Error State Placeholder -->
                <div wire:loading.remove wire:target="sendResetLink">
                    @error('email')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
