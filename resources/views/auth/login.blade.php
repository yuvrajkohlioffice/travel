<x-guest-layout>
    <!-- Full page background with optional gradient -->
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4 sm:px-6 lg:px-8">
        
        <!-- Card container -->
        <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 transition-all duration-300">
            
            <!-- Logo -->
            <div class="flex justify-center">
                <x-authentication-card-logo class="h-16 w-auto" />
            </div>

            <!-- Page heading -->
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                Sign in to your account
            </h2>


            <!-- Validation Errors -->
            <x-validation-errors class="mt-4" />

            <!-- Session Status -->
            @if (session('status'))
                <div class="mt-4 p-3 text-green-700 bg-green-100 rounded-md text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Login Form -->
            <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="rounded-md shadow-sm -space-y-px">
                    <!-- Email Input -->
                    <div class="mb-4">
                        <x-label for="email" value="{{ __('Email') }}" class="sr-only" />
                        <x-input 
                            id="email" 
                            name="email" 
                            type="email" 
                            :value="old('email')" 
                            required 
                            autofocus 
                            autocomplete="username"
                            placeholder="Enter your email"
                            class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 focus:z-10 sm:text-sm transition-all"
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
                            autocomplete="current-password" 
                            placeholder="Enter your password"
                            class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 focus:z-10 sm:text-sm transition-all"
                        />
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="flex items-center space-x-2">
                        <x-checkbox id="remember_me" name="remember" />
                        <span class="text-sm text-gray-600 dark:text-gray-300">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                            Forgot your password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div>
                    <x-button class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        <!-- Optional icon inside button -->
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <!-- Heroicon lock -->
                            <svg class="h-5 w-5 text-indigo-500 dark:text-indigo-200 group-hover:text-indigo-400 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6" />
                            </svg>
                        </span>
                        Log in
                    </x-button>
                </div>
            </form>

            <!-- Optional: Loading state -->
            <div wire:loading wire:target="login" class="mt-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                Logging in...
            </div>

            <!-- Optional: Error state -->
           
        </div>
    </div>
</x-guest-layout>
