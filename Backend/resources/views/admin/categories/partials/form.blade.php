@if ($errors->any())
    <div class="p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-6">

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Category Name <span
                class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Image</label>
        <div class="relative flex items-center bg-white-800 border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
            <label class="cursor-pointer bg-gray-200 px-5 py-2.5 text-sm font-medium text-gray-400 border-r border-gray-300">
                Choose file
                <input type="file" name="image" accept="image/*" class="hidden"
                    onchange="this.closest('.space-y-6').querySelector('.file-name-display').textContent = this.files.length > 0 ? this.files[0].name : 'No file chosen'">
            </label>
            <div class="file-name-display flex-1 px-4 py-2.5 text-sm text-gray-700 truncate">
                No file chosen
            </div>
        </div>
        @if(!empty($category?->image_url))
            <div class="mt-3">
                <img src="{{ $category->image_url }}"
                     alt="{{ $category->name }}"
                     class="w-20 h-20 object-cover rounded-lg border border-gray-300">
            </div>
        @endif
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
        <textarea name="description" rows="4"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $category->description ?? '') }}</textarea>
    </div>

    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))
            class="rounded border-gray-300 text-blue-600">
        <span class="text-sm font-medium text-gray-700">Active</span>
    </label>

</div>
