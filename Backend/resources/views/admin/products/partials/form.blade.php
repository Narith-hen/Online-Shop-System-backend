@php $knownFields = ['name', 'category_id', 'price', 'stock', 'image', 'image_url']; @endphp
@if ($errors->any() && collect($errors->keys())->diff($knownFields)->isNotEmpty())
    <div class="p-3 bg-red-50 border border-red-200 rounded-lg mb-4">
        <ul class="list-disc list-inside text-sm text-red-700">
            @foreach ($errors->keys() as $key)
                @if (!in_array($key, $knownFields))
                    @foreach ($errors->get($key) as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                @endif
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm @error('name') field-invalid @enderror" required>
        @error('name') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Category <span class="text-red-500">*</span></label>
        <select name="category_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm @error('category_id') field-invalid @enderror" required>
            <option value="">Select category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id ?? '') === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Price <span class="text-red-500">*</span></label>
            <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price ?? '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm @error('price') field-invalid @enderror" required>
            @error('price') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Stock <span class="text-red-500">*</span></label>
            <input type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm @error('stock') field-invalid @enderror" required>
            @error('stock') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Image</label>

        @php
            $isUrl = $product && $product->image && (str_starts_with($product->image, 'http://') || str_starts_with($product->image, 'https://'));
            $existingUrl = $product ? ($isUrl ? $product->image : Storage::url($product->image)) : '';
        @endphp

        <div class="border border-gray-200 rounded-lg p-4 space-y-4 bg-gray-50/50">
            <div id="preview-box" class="flex items-center justify-center bg-white rounded-lg border border-dashed border-gray-300 overflow-hidden relative {{ $product && $product->image || old('image_url') ? '' : 'hidden' }}" style="min-height:120px">
                <img id="main-preview"
                     src="{{ old('image_url') ?: $existingUrl }}"
                     alt="Image preview"
                     class="max-h-32 max-w-full object-contain p-2"
                     onerror="this.parentElement.classList.add('hidden')">
                <button type="button" onclick="clearImage()"
                    class="absolute top-1 right-1 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs transition shadow-sm"
                    title="Remove image">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <input type="hidden" name="remove_image" id="remove-image" value="0">

            <div class="space-y-2">
                <label class="text-xs font-medium text-gray-500">Paste image URL</label>
                <input type="url" name="image_url" id="url-input"
                    value="{{ old('image_url', $isUrl ? $product->image : '') }}"
                    placeholder="https://example.com/image.jpg"
                    oninput="previewUrl(this.value)"
                    class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400">Enter any image URL — it will be used as the product image.</p>
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
                        <input type="file" name="image" accept="image/*" class="hidden" onchange="handleFile(this)">
                    </label>
                    <span id="file-name" class="text-sm text-gray-500">No file chosen</span>
                </div>
            </div>
            @error('image') <p class="field-error">{{ $message }}</p> @enderror
            @error('image_url') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <label class="inline-flex items-center gap-2 cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="rounded border-gray-300 text-blue-600">
        <span class="text-sm font-medium text-gray-700">Active</span>
    </label>
</div>

<script>
function previewUrl(url) {
    var preview = document.getElementById('main-preview');
    var box = document.getElementById('preview-box');
    url = url.trim();
    if (url) {
        preview.src = url;
        box.classList.remove('hidden');
        document.getElementById('remove-image').value = '0';
    } else {
        preview.src = '';
        box.classList.add('hidden');
    }
}

function handleFile(input) {
    var display = document.getElementById('file-name');
    if (input.files.length > 0) {
        display.textContent = input.files[0].name;
        var preview = document.getElementById('main-preview');
        var box = document.getElementById('preview-box');
        document.getElementById('remove-image').value = '0';
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

function clearImage() {
    document.getElementById('url-input').value = '';
    document.getElementById('main-preview').src = '';
    document.getElementById('preview-box').classList.add('hidden');
    document.getElementById('remove-image').value = '1';
    document.getElementById('file-name').textContent = 'No file chosen';
    var fi = document.querySelector('input[name="image"]');
    if (fi) fi.value = '';
}

(function() {
    var urlInput = document.getElementById('url-input');
    if (urlInput && urlInput.value.trim()) {
        previewUrl(urlInput.value);
    }
})();
</script>
