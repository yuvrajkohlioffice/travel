<x-guest-layout>
    <!-- Page wrapper with background gradient -->
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4 sm:px-6 lg:px-8">
        
        <!-- Card container -->
        <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 transition-all duration-300">
            
            <!-- Logo -->
            <div class="flex justify-center">
                <x-authentication-card-logo class="h-16 w-auto" />
            </div>

            <!-- Page heading -->
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                    Log in
                </a>
            </p>

            <!-- Validation Errors -->
            <x-validation-errors class="mt-4" />

            <!-- Registration Form -->
            <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
                @csrf

                <div class="rounded-md shadow-sm -space-y-px">
                    <!-- Name Input -->
                    <div>
                        <x-label for="name" value="{{ __('Name') }}" class="sr-only" />
                        <x-input
                            id="name"
                            name="name"
                            type="text"
                            :value="old('name')"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Full Name"
                            class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm transition-all"
                        />
                    </div>

                    <!-- Email Input -->
                    <div class="mt-4">
                        <x-label for="email" value="{{ __('Email') }}" class="sr-only" />
                        <x-input
                            id="email"
                            name="email"
                            type="email"
                            :value="old('email')"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                            class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm transition-all"
                        />
                    </div>

                    <!-- Password Input -->
                    <div class="mt-4">
                        <x-label for="password" value="{{ __('Password') }}" class="sr-only" />
                        <x-input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="new-password"
                            placeholder="Password"
                            class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm transition-all"
                        />
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="mt-4">
                        <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="sr-only" />
                        <x-input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            placeholder="Confirm Password"
                            class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm transition-all"
                        />
                    </div>
                </div>

                <!-- Terms & Privacy -->
                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="mt-4">
                        <x-label for="terms">
                            <div class="flex items-center">
                                <x-checkbox name="terms" id="terms" required />

                                <div class="ms-2 text-gray-600 dark:text-gray-300 text-sm">
                                    {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">'.__('Privacy Policy').'</a>',
                                    ]) !!}
                                </div>
                            </div>
                        </x-label>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-4">
                    <x-button class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        Register
                    </x-button>
                </div>

                <!-- Livewire Loading State -->
                <div wire:loading wire:target="register" class="mt-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                    Registering...
                </div>

                <!-- Livewire Error State -->
                <div wire:loading.remove wire:target="register">
                    @error('name')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                    @error('email')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                    @error('password')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                </div>

            </form>
        </div>
    </div>
</x-guest-layout>
