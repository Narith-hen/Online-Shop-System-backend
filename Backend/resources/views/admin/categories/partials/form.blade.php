@if ($errors->any())
    <div class="p-3 bg-red-50 border border-red-200 rounded-lg mb-4">
        <ul class="list-disc list-inside text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Category Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Image</label>

        @php
            $catIsUrl = $category && $category->image && (str_starts_with($category->image, 'http://') || str_starts_with($category->image, 'https://'));
            $catExistingUrl = $category ? ($catIsUrl ? $category->image : Storage::url($category->image)) : '';
        @endphp

        <div class="border border-gray-200 rounded-lg p-4 space-y-4 bg-gray-50/50">
            <div id="cat-preview-box" class="flex items-center justify-center bg-white rounded-lg border border-dashed border-gray-300 overflow-hidden relative {{ $category && $category->image || old('image_url') ? '' : 'hidden' }}" style="min-height:100px">
                <img id="cat-preview"
                     src="{{ old('image_url') ?: $catExistingUrl }}"
                     alt="Image preview"
                     class="max-h-28 max-w-full object-contain p-2"
                     onerror="this.parentElement.classList.add('hidden')">
                <button type="button" onclick="clearCatImage()"
                    class="absolute top-1 right-1 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs transition shadow-sm"
                    title="Remove image">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <input type="hidden" name="remove_image" id="cat-remove-image" value="0">

            <div class="space-y-2">
                <label class="text-xs font-medium text-gray-500">Paste image URL</label>
                <input type="url" name="image_url" id="cat-url-input"
                    value="{{ old('image_url', $catIsUrl ? $category->image : '') }}"
                    placeholder="https://example.com/category-image.jpg"
                    oninput="previewCatUrl(this.value)"
                    class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400">Enter any image URL — it will be used as the category image.</p>
            </div>

            <div class="flex items-center gap-3">
                <hr class="flex-1 border-gray-200">
                <span class="text-xs text-gray-400 font-medium">OR</span>
                <hr class="flex-1 border-gray-200">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-medium text-gray-500">Upload file</label>
                <div class="flex items-center gap-3">
                    <label class="cursor-pointer px-4 py-2.5 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 transition flex items-center gap-2">
                        <i class="fas fa-cloud-upload-alt text-blue-500"></i>
                        Choose File
                        <input type="file" name="image" accept="image/*" class="hidden" onchange="handleCatFile(this)">
                    </label>
                    <span id="cat-file-name" class="text-sm text-gray-500">No file chosen</span>
                </div>
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
        <textarea name="description" rows="4"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">{{ old('description', $category->description ?? '') }}</textarea>
    </div>

    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))
            class="rounded border-gray-300 text-blue-600">
        <span class="text-sm font-medium text-gray-700">Active</span>
    </label>
</div>

<script>
function previewCatUrl(url) {
    var preview = document.getElementById('cat-preview');
    var box = document.getElementById('cat-preview-box');
    url = url.trim();
    if (url) {
        preview.src = url;
        box.classList.remove('hidden');
        document.getElementById('cat-remove-image').value = '0';
    } else {
        preview.src = '';
        box.classList.add('hidden');
    }
}

function handleCatFile(input) {
    var display = document.getElementById('cat-file-name');
    if (input.files.length > 0) {
        display.textContent = input.files[0].name;
        var preview = document.getElementById('cat-preview');
        var box = document.getElementById('cat-preview-box');
        document.getElementById('cat-remove-image').value = '0';
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            box.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        display.textContent = 'No file chosen';
    }
}

function clearCatImage() {
    document.getElementById('cat-url-input').value = '';
    document.getElementById('cat-preview').src = '';
    document.getElementById('cat-preview-box').classList.add('hidden');
    document.getElementById('cat-remove-image').value = '1';
    document.getElementById('cat-file-name').textContent = 'No file chosen';
    var fi = document.querySelector('input[name="image"]');
    if (fi) fi.value = '';
}

(function() {
    var input = document.getElementById('cat-url-input');
    if (input && input.value.trim()) previewCatUrl(input.value);
})();
</script>
