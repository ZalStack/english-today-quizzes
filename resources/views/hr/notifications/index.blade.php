@extends('layouts.hr')

@section('title', 'Notifikasi')

@section('header-title', 'Notifikasi')
@section('header-subtitle', 'Semua notifikasi yang Anda terima')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Header Stats --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500">
                <span class="font-semibold text-gray-700">{{ $notifications->total() }}</span> notifikasi total
                @if($unreadCount > 0)
                    &middot; <span class="font-semibold text-primary-600">{{ $unreadCount }}</span> belum dibaca
                @endif
            </p>
        </div>
        @if($unreadCount > 0)
            <button onclick="markAllRead()" class="hr-btn-secondary text-sm !py-2 !px-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tandai Semua Dibaca
            </button>
        @endif
    </div>

    {{-- Notification List --}}
    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div id="notification-{{ $notification->id }}"
                     class="hr-card p-4 sm:p-5 flex items-start gap-3 sm:gap-4 transition-all duration-200 cursor-pointer hover:shadow-soft-md {{ $notification->is_read ? '' : 'border-l-4 border-l-primary-500 bg-primary-50/20' }}"
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
        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="hr-card">
            <div class="hr-empty">
                <div class="hr-empty__icon">
                    <svg class="w-8 h-8 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Notifikasi</h3>
                <p class="text-sm text-gray-500">Notifikasi baru akan muncul di sini</p>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
async function markAsRead(id, el) {
    try {
        const res = await fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        if (res.ok) {
            el.classList.remove('border-l-4', 'border-l-primary-500', 'bg-primary-50/20');
            const dot = el.querySelector('.bg-primary-500.rounded-full');
            if (dot) dot.remove();
        }
    } catch (e) {}
}

async function markAllRead() {
    try {
        const res = await fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        if (res.ok) {
            location.reload();
        }
    } catch (e) {}
}
</script>
@endpush
@endsection
