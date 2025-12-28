<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">

        {{-- ================= PROFILE PHOTO ================= --}}
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div class="col-span-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">
                        Profile Photo
                    </h3>

                    <div x-data="{ photoName: null, photoPreview: null }" class="flex items-center gap-6">
                        <input type="file" id="photo" class="hidden" wire:model.live="photo" x-ref="photo"
                            x-on:change="
                            photoName = $refs.photo.files[0].name;
                            const reader = new FileReader();
                            reader.onload = (e) => photoPreview = e.target.result;
                            reader.readAsDataURL($refs.photo.files[0]);
                        " />

                        <div>
                            <div x-show="!photoPreview">
                                <img src="{{ $this->user->profile_photo_url }}"
                                    class="rounded-full size-20 object-cover">
                            </div>

                            <div x-show="photoPreview" style="display:none;">
                                <span class="block rounded-full size-20 bg-cover bg-center"
                                    x-bind:style="'background-image:url(' + photoPreview + ')'"></span>
                            </div>
                        </div>

                        <div>
                            <x-secondary-button type="button" x-on:click.prevent="$refs.photo.click()">
                                Select New Photo
                            </x-secondary-button>

                            @if ($this->user->profile_photo_path)
                                <x-secondary-button class="mt-2" wire:click="deleteProfilePhoto">
                                    Remove Photo
                                </x-secondary-button>
                            @endif

                            <x-input-error for="photo" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ================= BASIC INFORMATION ================= --}}
        <div class="col-span-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-6 text-gray-800 dark:text-gray-200">
                    Account Information
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    <div>
                        <x-label for="name" value="Name" />
                        <x-input id="name" wire:model="state.name" class="mt-1 w-full" />
                        <x-input-error for="name" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="email" value="Email" />
                        <x-input id="email" type="email" wire:model="state.email" class="mt-1 w-full" />
                        <x-input-error for="email" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="whatsapp_api_key" value="WhatsApp API Key" />
                        <x-input id="whatsapp_api_key" wire:model.defer="state.whatsapp_api_key"
                            placeholder="Enter WhatsApp API key" class="mt-1 w-full" />
                        <x-input-error for="whatsapp_api_key" class="mt-2" />
                    </div>

                    @if (auth()->user()->role_id == 1)
                        <div>
                            <x-label for="company_id" value="Company" />
                            <select id="company_id" wire:model.defer="state.company_id"
                                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Select Company</option>
                                @foreach (\App\Models\Company::all() as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="company_id" class="mt-2" />
                        </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- ================= SMTP SETTINGS ================= --}}
        <div class="col-span-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-indigo-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-6 text-indigo-600 dark:text-indigo-400">
                    SMTP Email Settings
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    <div>
                        <x-label value="SMTP Host" />
                        <x-input wire:model.defer="state.smtp_host" placeholder="smtp.gmail.com" class="mt-1 w-full" />
                    </div>

                    <div>
                        <x-label value="SMTP Port" />
                        <x-input type="number" wire:model.defer="state.smtp_port" placeholder="587"
                            class="mt-1 w-full" />
                    </div>

                    <div>
                        <x-label value="Encryption" />
                        <select wire:model.defer="state.smtp_encryption"
                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                            <option value="">None</option>
                            <option value="tls">TLS</option>
                            <option value="ssl">SSL</option>
                        </select>
                    </div>

                    <div>
                        <x-label value="SMTP Username" />
                        <x-input wire:model.defer="state.smtp_username" class="mt-1 w-full" />
                    </div>

                    <div>
                        <x-label value="SMTP Password" />
                        <x-input type="password" wire:model.defer="state.smtp_password" class="mt-1 w-full"
                            placeholder="Leave blank to keep existing" autocomplete="new-password" />

                    </div>

                    <div>
                        <x-label value="From Email" />
                        <x-input type="email" wire:model.defer="state.smtp_from_email" class="mt-1 w-full" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-label value="From Name" />
                        <x-input wire:model.defer="state.smtp_from_name" class="mt-1 w-full" />
                    </div>

                </div>
            </div>
        </div>

    </x-slot>


    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
