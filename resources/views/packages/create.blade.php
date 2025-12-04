<x-app-layout>
    <div class="ml-64 flex justify-center p-6">
        <div class="w-full max-w-5xl bg-white dark:bg-gray-800 rounded shadow-lg p-6">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add Package</h2>
                <a href="{{ route('packages.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    ← Back
                </a>
            </div>

            <!-- Form -->
            <form action="{{ route('packages.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" enctype="multipart/form-data">
                @csrf

                <!-- Package Name -->
                <div class="col-span-1 lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Package Name</label>
                    <input type="text" name="package_name" class="mt-1 block w-full border rounded px-4 py-2" required>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Type</label>
                    <select name="package_type_id" class="mt-1 block w-full border rounded px-4 py-2" required>
                        <option value="">Select Type</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Category</label>
                    <select name="package_category_id" class="mt-1 block w-full border rounded px-4 py-2" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Difficulty -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Difficulty</label>
                    <select name="difficulty_type_id" class="mt-1 block w-full border rounded px-4 py-2" required>
                        <option value="">Select Difficulty</option>
                        @foreach($difficulties as $difficulty)
                            <option value="{{ $difficulty->id }}">{{ $difficulty->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Days -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Days</label>
                    <input type="number" name="package_days" class="mt-1 block w-full border rounded px-4 py-2" required>
                </div>

                <!-- Nights -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nights</label>
                    <input type="number" name="package_nights" class="mt-1 block w-full border rounded px-4 py-2" required>
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Price (Also Add 20% Company Profit )</label>
                    <input type="number" step="0.01" name="package_price" class="mt-1 block w-full border rounded px-4 py-2" required>
                </div>

                <!-- Pickup Points -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Pickup Points</label>
                    <input type="text" name="pickup_points" class="mt-1 block w-full border rounded px-4 py-2">
                </div>

                <!-- Altitude -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Altitude</label>
                    <input type="text" name="altitude" class="mt-1 block w-full border rounded px-4 py-2">
                </div>

                <!-- Age Min -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Min Age</label>
                    <input type="number" name="min_age" class="mt-1 block w-full border rounded px-4 py-2">
                </div>

                <!-- Age Max -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Max Age</label>
                    <input type="number" name="max_age" class="mt-1 block w-full border rounded px-4 py-2">
                </div>

                <!-- Best Time -->
                <div class="col-span-1 lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Best Time to Visit</label>
                    <input type="text" name="best_time_to_visit" class="mt-1 block w-full border rounded px-4 py-2">
                </div>

                <!-- Package Docs -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Package Docs</label>
                    <input type="file" name="package_docs" class="mt-1 block w-full border rounded px-4 py-2" accept=".pdf,.doc,.docx">
                </div>

                <!-- Package Banner -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Package Banner</label>
                    <input type="file" name="package_banner" class="mt-1 block w-full border rounded px-4 py-2" accept="image/*">
                </div>

                <!-- Other Images -->
                <div class="col-span-1 lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Other Images</label>
                    <input type="file" name="other_images[]" class="mt-1 block w-full border rounded px-4 py-2" accept="image/*" multiple>
                </div>

                <!-- Content -->
                <div class="col-span-1 lg:col-span-3">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Content</label>
    <textarea id="package-content" name="content" rows="4" class="mt-1 block w-full border rounded px-4 py-2 tinymce-editor" required></textarea>
</div>


                <!-- Submit -->
                <div class="col-span-1 lg:col-span-3">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Save Package
                    </button>
                </div>
            </form>
        </div>
    </div>
       <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            tinymce.init({
                selector: '.tinymce-editor',
                promotion: false,
                branding: false,
                menubar: false,
                plugins: 'link lists table code help',
                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link table | code help',
                skin: 'oxide',
                content_css: 'default',
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                }
            });
        });
    </script>
</x-app-layout>
