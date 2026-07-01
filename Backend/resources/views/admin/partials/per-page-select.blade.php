@php $currentPerPage = isset($_COOKIE['per_page']) ? (int) $_COOKIE['per_page'] : 10; @endphp
<div class="flex items-center gap-2 text-sm text-gray-500">
    <label for="per-page-select" class="whitespace-nowrap">Rows per page</label>
    <select id="per-page-select" onchange="setPerPage(this.value)" class="px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        @foreach([5, 10, 15, 25] as $size)
            <option value="{{ $size }}" {{ $currentPerPage === $size ? 'selected' : '' }}>{{ $size }}</option>
        @endforeach
    </select>
</div>
