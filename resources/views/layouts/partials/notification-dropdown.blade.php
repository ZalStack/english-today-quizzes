{{-- Notification Dropdown Component --}}
<div class="relative" x-data="notificationDropdown()" x-init="init()" @click.outside="close()">
    {{-- Bell Button --}}
    <button @click="toggle()" class="relative p-1.5 sm:p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200 tap-target rounded-xl hover:bg-gray-100">
        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        <span x-show="unreadCount > 0" x-cloak
              class="absolute top-0.5 right-0.5 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-pulse"
              x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         class="absolute right-0 top-full mt-2 w-[340px] sm:w-[380px] bg-white rounded-2xl shadow-soft-lg border border-gray-100/80 z-50 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-gray-900">Notifikasi</h3>
                <span x-show="unreadCount > 0" x-cloak
                      class="px-1.5 py-0.5 bg-primary-50 text-primary-600 text-[10px] font-bold rounded-full"
                      x-text="unreadCount"></span>
            </div>
            <div class="flex items-center gap-1">
                <button x-show="unreadCount > 0" x-cloak
                        @click="markAllRead()" :disabled="markingAll"
                        class="text-[11px] font-medium text-primary-600 hover:text-primary-700 transition-colors px-2 py-1 rounded-lg hover:bg-primary-50">
                    <span x-show="!markingAll">Tandai semua dibaca</span>
                    <span x-show="markingAll" class="flex items-center gap-1">
                        <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Loading...
                    </span>
                </button>
            </div>
        </div>

        {{-- Loading State --}}
        <div x-show="loading" class="py-8 text-center">
            <svg class="animate-spin w-6 h-6 mx-auto text-primary-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <p class="text-xs text-gray-400 mt-2">Memuat notifikasi...</p>
        </div>

        {{-- Empty State --}}
        <div x-show="!loading && items.length === 0" class="py-10 text-center px-4">
            <div class="w-14 h-14 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500">Belum ada notifikasi</p>
            <p class="text-xs text-gray-400 mt-1">Notifikasi baru akan muncul di sini</p>
        </div>

        {{-- Notification List --}}
        <div x-show="!loading && items.length > 0" class="max-h-[360px] overflow-y-auto custom-scrollbar">
            <template x-for="(item, index) in items" :key="item.id">
                <div @click="handleClick(item)"
                     class="flex items-start gap-3 px-5 py-3.5 border-b border-gray-50 last:border-0 cursor-pointer transition-all duration-150 hover:bg-gray-50/80"
                     :class="{ 'bg-primary-50/30': !item.is_read }">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center mt-0.5"
                         :class="item.color">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="item.icon"></svg>
                    </div>
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-[13px] font-semibold text-gray-900 leading-snug" x-text="item.title"></p>
                            <div x-show="!item.is_read" class="flex-shrink-0 w-2 h-2 bg-primary-500 rounded-full mt-1.5"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2 leading-relaxed" x-text="item.message"></p>
                        <p class="text-[11px] text-gray-400 mt-1" x-text="item.created_at"></p>
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div x-show="!loading && totalNotifications > 0" class="border-t border-gray-100 px-5 py-3 bg-gray-50/50">
            <a href="{{ url(auth()->user()->isHR() ? 'hr' : 'employee') }}/notifications"
               class="flex items-center justify-center gap-1.5 text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors py-1.5 rounded-lg hover:bg-primary-50">
                <span>Lihat Semua Notifikasi</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function notificationDropdown() {
    return {
        open: false,
        loading: false,
        markingAll: false,
        items: [],
        unreadCount: 0,
        totalNotifications: 0,

        init() {
            this.fetchUnreadCount();
            setInterval(() => this.fetchUnreadCount(), 60000);
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.fetchNotifications();
            }
        },

        close() {
            this.open = false;
        },

        async fetchUnreadCount() {
            try {
                const res = await fetch('/notifications/unread-count', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await res.json();
                this.unreadCount = data.count;
            } catch (e) {}
        },

        async fetchNotifications() {
            this.loading = true;
            try {
                const res = await fetch('/notifications/latest', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await res.json();
                this.items = data.notifications;
                this.unreadCount = data.total_unread;
                this.totalNotifications = data.total_notifications;
            } catch (e) {}
            this.loading = false;
        },

        async handleClick(item) {
            if (!item.is_read) {
                try {
                    await fetch(`/notifications/${item.id}/read`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    item.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                } catch (e) {}
            }
        },

        async markAllRead() {
            this.markingAll = true;
            try {
                await fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                this.items.forEach(i => i.is_read = true);
                this.unreadCount = 0;
            } catch (e) {}
            this.markingAll = false;
        }
    };
}
</script>
@endpush

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
