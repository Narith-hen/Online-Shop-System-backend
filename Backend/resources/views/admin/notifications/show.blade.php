@extends('MainLayout')

@section('title', 'Notification #' . $notification->id)
@section('page_title', 'Notification #' . $notification->id)
@section('page_subtitle', 'View notification details')

@section('content')
<div class="space-y-6">

    <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-blue-600 transition">
        <i class="fas fa-arrow-left"></i> Back to Notifications
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $notification->title }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $notification->created_at->format('F d, Y \a\t h:i A') }}</p>
                </div>
                @php
                    $typeStyles = ['news' => 'bg-blue-50 text-blue-700 border-blue-200', 'alert' => 'bg-red-50 text-red-600 border-red-200', 'update' => 'bg-emerald-50 text-emerald-700 border-emerald-200'];
                @endphp
                <span class="px-3 py-1.5 rounded-full text-xs font-medium border {{ $typeStyles[$notification->type] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                    {{ ucfirst($notification->type) }}
                </span>
            </div>

            <div class="text-gray-700 leading-relaxed">{{ $notification->message ?? 'No message content.' }}</div>

            @if($notification->link)
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Related Link</p>
                    <a href="{{ $notification->link }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <i class="fas fa-external-link-alt"></i> {{ $notification->link }}
                    </a>
                </div>
            @endif
        </div>

        <div class="card p-5">
            <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-users text-blue-500"></i> Recipients ({{ $reads->count() }})
            </h4>

            @if($reads->isEmpty())
                <p class="text-sm text-gray-500">No recipients yet.</p>
            @else
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($reads as $read)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $read->name }}</p>
                                <p class="text-xs text-gray-500">{{ $read->email }}</p>
                            </div>
                            <span class="text-xs font-medium {{ $read->read_at ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $read->read_at ? 'Read' : 'Unread' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-6 pt-4 border-t border-gray-100">
                <button onclick="showDeleteModal('Notification', {{ $notification->id }}, '{{ route('admin.notifications.destroy', $notification->id) }}', { redirectUrl: '{{ route('admin.notifications.index') }}' })"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium text-sm">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
