@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6 sm:mb-8">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Notifikasi</h1>
            <p class="text-sm text-gray-500 mt-1">
                Semua notifikasi yang Anda terima
            </p>
        </div>
        @if($unreadCount > 0)
            <button onclick="markAllRead()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tandai Semua Dibaca
            </button>
        @endif
    </div>

    {{-- Stats Bar --}}
    <div class="flex items-center gap-3 mb-5">
        <span class="inline-flex items-center px-3 py-1 bg-white border border-gray-100 rounded-lg text-xs font-medium text-gray-600 shadow-sm">
            <span class="font-bold text-gray-800 mr-1">{{ $notifications->total() }}</span> total
        </span>
        @if($unreadCount > 0)
            <span class="inline-flex items-center px-3 py-1 bg-primary-50 border border-primary-100 rounded-lg text-xs font-medium text-primary-700 shadow-sm">
                <span class="w-1.5 h-1.5 bg-primary-500 rounded-full mr-1.5 animate-pulse"></span>
                <span class="font-bold mr-1">{{ $unreadCount }}</span> belum dibaca
            </span>
        @endif
    </div>

    {{-- Notification List --}}
    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div id="notification-{{ $notification->id }}"
                     class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 flex items-start gap-3 sm:gap-4 transition-all duration-200 cursor-pointer hover:shadow-soft-md {{ $notification->is_read ? 'opacity-75 hover:opacity-100' : 'border-l-4 border-l-primary-500 bg-primary-50/20' }}"
                     onclick="markAsRead({{ $notification->id }}, this)">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center {{ $notification->getTypeColor() }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $notification->getTypeIcon() !!}
                        </svg>
                    </div>
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-gray-900">{{ $notification->title }}</h4>
                                @if(!$notification->is_read)
                                    <span class="flex-shrink-0 w-2 h-2 bg-primary-500 rounded-full"></span>
                                @endif
                            </div>
                            <span class="flex-shrink-0 text-[11px] text-gray-400 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ $notification->message }}</p>
                        @if($notification->sender)
                            <div class="flex items-center gap-1.5 mt-2">
                                <div class="w-5 h-5 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-[9px] font-bold">
                                    {{ strtoupper(substr($notification->sender->full_name ?? $notification->sender->name, 0, 1)) }}
                                </div>
                                <span class="text-xs text-gray-400">{{ $notification->sender->full_name ?? $notification->sender->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8 flex justify-center">
            {{ $notifications->onEachSide(1)->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Notifikasi</h3>
            <p class="text-sm text-gray-500">Notifikasi baru akan muncul di sini</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
async function markAsRead(id, el) {
    try {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const res = await fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token }
        });
        if (res.ok) {
            el.classList.remove('border-l-4', 'border-l-primary-500', 'bg-primary-50/20', 'opacity-75');
            el.classList.add('opacity-100');
            const dot = el.querySelector('.bg-primary-500.rounded-full');
            if (dot) dot.remove();
        }
    } catch (e) {}
}

async function markAllRead() {
    try {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const res = await fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token }
        });
        if (res.ok) {
            location.reload();
        }
    } catch (e) {}
}
</script>
@endpush
@endsection
