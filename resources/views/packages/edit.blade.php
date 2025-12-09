<x-app-layout>
    <div class="ml-64 flex justify-center p-6">
        <div class="w-full max-w-5xl bg-white dark:bg-gray-800 rounded shadow-lg p-6">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Package</h2>
                <a href="{{ route('packages.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    ← Back
                </a>
            </div>

            <!-- Form -->
            <form action="{{ route('packages.update', $package->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Package Name -->
                <div class="col-span-1 lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Package Name</label>
                    <input type="text" name="package_name" value="{{ $package->package_name }}" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition" required>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Type</label>
                    <select name="package_type_id" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition" required>
                        <option value="">Select Type</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ $package->package_type_id == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Category</label>
                    <select name="package_category_id" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $package->package_category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Difficulty -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Difficulty</label>
                    <select name="difficulty_type_id" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition" required>
                        <option value="">Select Difficulty</option>
                        @foreach($difficulties as $difficulty)
                            <option value="{{ $difficulty->id }}" {{ $package->difficulty_type_id == $difficulty->id ? 'selected' : '' }}>
                                {{ $difficulty->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Days -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Days</label>
                    <input type="number" name="package_days" value="{{ $package->package_days }}" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition" required>
                </div>

                <!-- Nights -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nights</label>
                    <input type="number" name="package_nights" value="{{ $package->package_nights }}" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition" required>
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Price</label>
                    <input type="number" step="0.01" name="package_price" value="{{ $package->package_price }}" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition" required>
                </div>

                <!-- Pickup Points -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Pickup Points</label>
                    <input type="text" name="pickup_points" value="{{ $package->pickup_points }}" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <!-- Altitude -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Altitude</label>
                    <input type="text" name="altitude" value="{{ $package->altitude }}" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <!-- Age Min -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Min Age</label>
                    <input type="number" name="min_age" value="{{ $package->min_age }}" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <!-- Age Max -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Max Age</label>
                    <input type="number" name="max_age" value="{{ $package->max_age }}" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <!-- Best Time -->
                <div class="col-span-1 lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Best Time to Visit</label>
                    <input type="text" name="best_time_to_visit" value="{{ $package->best_time_to_visit }}" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <!-- Package Docs -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Package Docs</label>
                    @if($package->package_docs)
                        <div class="mb-2">
                            <a href="{{ asset('storage/'.$package->package_docs) }}" target="_blank" class="text-blue-600 underline">View Current Doc</a>
                        </div>
                    @endif
                    <input type="file" name="package_docs" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition" accept=".pdf,.doc,.docx">
                </div>

                <!-- Package Banner -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Package Banner</label>
                    @if($package->package_banner)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$package->package_banner) }}" alt="Banner" class="h-32 w-full object-cover rounded">
                        </div>
                    @endif
                    <input type="file" name="package_banner" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition" accept="image/*">
                </div>

                <!-- Other Images -->
                <div class="col-span-1 lg:col-span-3">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Other Images</label>
    <div class="flex flex-wrap gap-2 mb-2">
        @if($package->other_images)
            @foreach($package->other_images as $image)
                <img src="{{ asset('storage/'.$image) }}" class="h-24 w-24 object-cover rounded" alt="Other Image">
            @endforeach
        @endif
    </div>
    <input type="file" name="other_images[]" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition" accept="image/*" multiple>
</div>

                <!-- Content -->
                <div class="col-span-1 lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Content</label>
                    <textarea id="package-content" name="content" rows="6" class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 w-full text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition tinymce-editor" required>{{ $package->content }}</textarea>
                </div>

                <!-- Submit -->
                <div class="col-span-1 lg:col-span-3">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Update Package
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TinyMCE -->
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
