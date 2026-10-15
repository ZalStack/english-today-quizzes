@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 pb-14 sm:pb-16">

    {{-- ================= PAGE HEADER ================= --}}
    <header class="et-reveal mb-6 sm:mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <p class="et-eyebrow">🔔 Pusat Informasi</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Notifikasi</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Semua pembaruan, pengumuman, dan aktivitas terbaru untukmu.
            </p>
        </div>
        @if($unreadCount > 0)
            <button type="button" onclick="markAllRead()"
                    class="et-btn et-btn-soft et-wiggle et-press shrink-0 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tandai Semua Dibaca
            </button>
        @endif
    </header>

    {{-- ================= STATS ================= --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="et-stat et-reveal et-delay-1">
            <div class="flex items-center justify-between gap-2">
                <div class="et-stat__icon bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <span class="et-badge et-badge-info">Total</span>
            </div>
            <p class="et-stat__value mt-3">{{ $notifications->total() }}</p>
            <p class="et-stat__label mt-0.5">Total notifikasi</p>
        </div>

        <div class="et-stat et-reveal et-delay-2">
            <div class="flex items-center justify-between gap-2">
                <div class="et-stat__icon {{ $unreadCount > 0 ? 'bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400' : 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="et-badge {{ $unreadCount > 0 ? 'et-badge-warning' : 'et-badge-success' }}">
                    {{ $unreadCount > 0 ? 'Perlu dibaca' : 'Semua dibaca' }}
                </span>
            </div>
            <p class="et-stat__value mt-3">{{ $unreadCount }}</p>
            <p class="et-stat__label mt-0.5">Belum dibaca</p>
        </div>
    </div>

    {{-- ================= NOTIFICATION LIST ================= --}}
    <section class="et-panel et-reveal">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="et-section-title">📥 Semua Notifikasi</h2>
                <p class="et-section-sub">Klik satu notifikasi untuk menandainya telah dibaca</p>
            </div>
            @if($unreadCount > 0)
                <span class="et-badge et-badge-warning">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 et-pulse-dot"></span>
                    {{ $unreadCount }} belum dibaca
                </span>
            @else
                <span class="et-badge et-badge-success">✔ Semua terbaca</span>
            @endif
        </div>

        <div class="p-4 sm:p-5">
            @if($notifications->count() > 0)
                <div class="space-y-3">
                    @foreach($notifications as $notification)
                        @php
                            // Varian dark-mode untuk warna ikon (getTypeColor() tetap dipakai)
                            $etDarkTone = match ($notification->type) {
                                'video_submission' => 'dark:bg-purple-500/15 dark:text-purple-300',
                                'material_upload' => 'dark:bg-emerald-500/15 dark:text-emerald-300',
                                'challenge_upload' => 'dark:bg-orange-500/15 dark:text-orange-300',
                                'quiz_upload' => 'dark:bg-sky-500/15 dark:text-sky-300',
                                default => 'dark:bg-slate-500/15 dark:text-slate-300',
                            };
                        @endphp

                        <article id="notification-{{ $notification->id }}"
                                 onclick="markAsRead({{ $notification->id }}, this)"
                                 class="et-card et-lift relative overflow-hidden p-4 sm:p-5 flex items-start gap-3 sm:gap-4 cursor-pointer transition-all {{ $notification->is_read ? 'opacity-70 hover:opacity-100' : 'hover:opacity-100' }}">

                            {{-- Unread: garis gradien di tepi kiri --}}
                            @if(!$notification->is_read)
                                <span class="et-notif-unread-bar absolute inset-y-0 left-0 w-[3px] bg-gradient-to-b from-lime-500 via-emerald-400 to-lime-500" aria-hidden="true"></span>
                            @endif

                            {{-- Ikon tipe notifikasi --}}
                            <div class="relative w-10 h-10 sm:w-11 sm:h-11 rounded-2xl flex items-center justify-center shrink-0 {{ $notification->getTypeColor() }} {{ $etDarkTone }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $notification->getTypeIcon() !!}
                                </svg>
                            </div>

                            {{-- Konten --}}
                            <div class="relative flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2 sm:gap-3">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 truncate">{{ $notification->title }}</h3>
                                        @if(!$notification->is_read)
                                            <span class="et-notif-dot w-2 h-2 rounded-full bg-lime-500 et-pulse-dot shrink-0" aria-label="Belum dibaca"></span>
                                        @endif
                                    </div>
                                    <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 whitespace-nowrap shrink-0">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">{{ $notification->message }}</p>

                                @if($notification->sender)
                                    <div class="flex items-center gap-2 mt-2.5 min-w-0">
                                        <span class="w-6 h-6 rounded-full bg-gradient-to-br from-violet-500 to-sky-500 text-white flex items-center justify-center text-[10px] font-extrabold shrink-0">
                                            {{ strtoupper(substr($notification->sender->full_name ?? $notification->sender->name, 0, 1)) }}
                                        </span>
                                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">
                                            {{ $notification->sender->full_name ?? $notification->sender->name }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Penanda status --}}
                            <span class="et-notif-status relative shrink-0 self-center hidden sm:inline-flex items-center gap-1 text-[11px] font-extrabold {{ $notification->is_read ? 'text-slate-400 dark:text-slate-500' : 'text-lime-600 dark:text-lime-400' }}">
                                {{ $notification->is_read ? 'Dibaca' : 'Baru' }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6 sm:mt-8 flex justify-center">
                    {{ $notifications->onEachSide(1)->links() }}
                </div>
            @else
                <div class="et-empty">
                    <div class="et-empty__icon et-mascot et-mascot--bounce">🔕</div>
                    <p class="et-empty__title">Belum Ada Notifikasi</p>
                    <p class="et-empty__text">Notifikasi baru seperti pengumuman HRD, materi, dan video challenge akan muncul di sini.</p>
                    <a href="{{ route('employee.dashboard') }}" class="et-btn et-btn-primary mt-5 et-press">🏠 Ke Dashboard</a>
                </div>
            @endif
        </div>
    </section>
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
            el.classList.add('opacity-70', 'hover:opacity-100');
            const bar = el.querySelector('.et-notif-unread-bar');
            if (bar) bar.remove();
            const dot = el.querySelector('.et-notif-dot');
            if (dot) dot.remove();
            const status = el.querySelector('.et-notif-status');
            if (status) {
                status.classList.remove('text-lime-600', 'dark:text-lime-400');
                status.classList.add('text-slate-400', 'dark:text-slate-500');
                status.textContent = 'Dibaca';
            }
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
