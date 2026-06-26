@if ($errors->any())
    <div class="p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
    <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        <option value="">Select category</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id ?? '') === (string) $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Price</label>
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Stock</label>
        <input type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
    </div>
</div>

{{-- <div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Image</label>
    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
    @if(!empty($product?->image_url))
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded mt-3">
    @endif
</div> --}}

<div>
    <label class="block text-sm font-semibold text-white-300 mb-2">Image</label>
    
    <div class="relative flex items-center bg-white-800 border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
        
        <!-- Choose File Button -->
        <label class="cursor-pointer bg-gray-200 px-5 py-2.5 text-sm font-medium text-gray-400 border-r border-gray-300">
            Choose file
            <input 
                type="file" 
                name="image" 
                accept="image/*" 
                class="hidden"
                onchange="updateFileName(this)">
        </label>
        
        <!-- File Name Display -->
        <div id="file-name-display" 
             class="flex-1 px-4 py-2.5 text-sm text-gray-700 truncate">
            No file chosen
        </div>
    </div>

    @if(!empty($product?->image_url))
        <div class="mt-3">
            <img src="{{ $product->image_url }}" 
                 alt="{{ $product->name }}" 
                 class="w-20 h-20 object-cover rounded-lg border border-gray-700">
        </div>
    @endif
</div>


<label class="inline-flex items-center gap-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="rounded border-gray-300 text-blue-600">
    <span class="text-sm font-medium text-gray-700">Active</span>
</label>

<script>
function updateFileName(input) {
    const display = document.getElementById('file-name-display');
    if (input.files.length > 0) {
        display.textContent = input.files[0].name;
        display.classList.add('text-gray-500');
    } else {
        display.textContent = 'No file chosen';
        display.classList.remove('text-gray-500');
    }
}
</script>