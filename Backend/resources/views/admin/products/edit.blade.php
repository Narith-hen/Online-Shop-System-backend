@extends('MainLayout')

@section('title', 'Edit Product')
@section('page_title', 'Edit Product')
@section('page_subtitle', 'Update product details')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-3xl">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        @include('admin.products.partials.form', ['product' => $product])

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg">Update</button>
            <a href="{{ route('admin.products.show', $product) }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
        </div>
    </form>
</div>
@endsection