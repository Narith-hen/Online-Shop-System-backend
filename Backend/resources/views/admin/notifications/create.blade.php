@extends('MainLayout')

@section('title', 'Create Notification')
@section('page_title', 'Create Notification')
@section('page_subtitle', 'Send a new notification to users')

@section('content')
<div class="space-y-6">

    <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-blue-600 transition">
        <i class="fas fa-arrow-left"></i> Back to Notifications
    </a>

    <div class="card p-6 max-w-2xl">
        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-bell text-blue-500"></i> New Notification
        </h3>

        <form method="POST" action="{{ route('admin.notifications.store') }}">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-semibold text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="news" {{ old('type') === 'news' ? 'selected' : '' }}>News</option>
                        <option value="alert" {{ old('type') === 'alert' ? 'selected' : '' }}>Alert</option>
                        <option value="update" {{ old('type') === 'update' ? 'selected' : '' }}>Update</option>
                    </select>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-semibold text-gray-700 mb-1.5">Message</label>
                    <textarea name="message" id="message" rows="4"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">{{ old('message') }}</textarea>
                    @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="link" class="block text-sm font-semibold text-gray-700 mb-1.5">Link (optional)</label>
                    <input type="url" name="link" id="link" value="{{ old('link') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    @error('link') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="notify_all" value="1" checked
                        class="mt-0.5 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Notify all users (with socket broadcast)</span>
                        <p class="text-xs text-gray-500 mt-0.5">When checked, this notification will be delivered to all users and pushed in real-time.</p>
                    </div>
                </label>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm shadow-sm">
                        <i class="fas fa-paper-plane"></i> Send Notification
                    </button>
                    <a href="{{ route('admin.notifications.index') }}" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
