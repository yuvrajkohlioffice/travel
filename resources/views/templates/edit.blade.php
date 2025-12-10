<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-4xl">

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-edit text-yellow-500"></i>
                        Edit Message Template
                    </h2>

                    <a href="{{ route('templates.index') }}"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <form method="POST" action="{{ route('templates.update', $template->id) }}"
                    enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Package Dropdown -->
                    <div>
                        <label class="font-semibold text-gray-800 dark:text-gray-200">Select Package</label>
                        <select name="package_id" class="w-full border rounded-lg p-3 dark:bg-gray-700 dark:text-white"
                            required>
                            <option value="">-- Select Package --</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}"
                                    {{ $template->package_id == $package->id ? 'selected' : '' }}>
                                    {{ $package->package_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <!-- WhatsApp -->
                    <div class="border p-4 rounded-lg dark:border-gray-700">
                        <h3 class="font-semibold mb-3 text-gray-700 dark:text-gray-200">WhatsApp Template</h3>

                        <label>Message Text</label>
                        <textarea name="whatsapp_text" rows="3" class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:text-white">{{ $template->whatsapp_text }}</textarea>

                        <label class="mt-3 block">Media File</label>
                        <input type="file" name="whatsapp_media"
                            class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:text-white">

                        @if ($template->whatsapp_media)
                            <p class="mt-2 text-blue-500">
                                Current: <a href="{{ asset('storage/' . $template->whatsapp_media) }}"
                                    target="_blank">View File</a>
                            </p>
                        @endif
                    </div>

                    <!-- Email -->
                    <div class="border p-4 rounded-lg dark:border-gray-700">
                        <h3 class="font-semibold mb-3 text-gray-700 dark:text-gray-200">Email Template</h3>

                        <label>Subject</label>
                        <input type="text" name="email_subject" value="{{ $template->email_subject }}"
                            class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:text-white">

                        <label class="mt-3 block">Body</label>
                        <textarea name="email_body" rows="4" class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:text-white">{{ $template->email_body }}</textarea>

                        <label class="mt-3 block">Attachment</label>
                        <input type="file" name="email_media"
                            class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:text-white">

                        @if ($template->email_media)
                            <p class="mt-2 text-blue-500">
                                Current: <a href="{{ asset('storage/' . $template->email_media) }}" target="_blank">View
                                    File</a>
                            </p>
                        @endif
                    </div>

                    <button class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                        Update Template
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
