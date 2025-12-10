<x-app-layout>

    <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full">
            <x-slot name="header">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Profile') }}
                </h2>
            </x-slot>

            <div class="py-10 sm:px-6 lg:px-8">

                @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                    @livewire('profile.update-profile-information-form')
                    <x-section-border />
                @endif

                @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                    <div class="mt-10 sm:mt-0">
                        @livewire('profile.update-password-form')
                    </div>
                    <x-section-border />
                @endif

                @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                    <div class="mt-10 sm:mt-0">
                        @livewire('profile.two-factor-authentication-form')
                    </div>
                    <x-section-border />
                @endif

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.logout-other-browser-sessions-form')
                </div>


            </div>

        </div>
    </div>
    

</x-app-layout>
