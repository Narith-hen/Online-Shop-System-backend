@extends('MainLayout')

@section('title', 'Create User')
@section('page_title', 'Create User')
@section('page_subtitle', 'Add a new user to the system')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-3xl">
    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
        @csrf

        @include('admin.users.partials.form', ['user' => null])

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg">Create</button>
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
        </div>
    </form>
</div>
@endsection
