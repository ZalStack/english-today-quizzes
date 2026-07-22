@extends('layouts.app')

@section('title', $videoChallenge->title . ' — Progress')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('hr.video-challenges.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">&larr; Kembali</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-extrabold text-gray-900">{{ $videoChallenge->title }}</h1>
                    @if($videoChallenge->description)
                        <p class="text-gray-500 mt-1">{{ $videoChallenge->description }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-2">Dibuat {{ $videoChallenge->created_at->format('d M Y H:i') }}</p>
                </div>
                <span class="px-3 py-1.5 {{ $videoChallenge->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} text-xs font-bold rounded-full">{{ $videoChallenge->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
                <p class="text-3xl font-extrabold text-gray-900">{{ $stats['total_employees'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Pegawai</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
                <p class="text-3xl font-extrabold text-green-600">{{ $stats['submitted'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Sudah Mengumpulkan</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
                <p class="text-3xl font-extrabold text-red-500">{{ $stats['pending'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Belum Mengumpulkan</p>
            </div>
        </div>

        @if($stats['total_employees'] > 0)
            <div class="w-full bg-gray-200 rounded-full h-3 mb-8">
                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-3 rounded-full transition-all duration-500"
                     style="width: {{ round(($stats['submitted'] / $stats['total_employees']) * 100) }}%"></div>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
            @forelse($divisionData as $index => $data)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-bold text-gray-900 text-sm truncate">{{ $data['division_name'] }}</h3>
                            <span class="text-xs text-gray-400">{{ $data['total'] }} org</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all"
                                 style="width: {{ $data['total'] > 0 ? round(($data['submitted_count'] / $data['total']) * 100) : 0 }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs mb-3">
                            <span class="text-green-600 font-semibold">{{ $data['submitted_count'] }} submitted</span>
                            <span class="text-red-500 font-semibold">{{ $data['pending_count'] }} pending</span>
                        </div>
                        <button onclick="openModal({{ $index }}, '{{ $data['division_name'] }}')"
                                class="w-full px-3 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition">
                            Detail
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-gray-100">
                    <p class="text-gray-500">Tidak ada pegawai aktif.</p>
                </div>
            @endforelse
        </div>

        {{-- Modal for employee list --}}
        <div id="employeeModal" class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <h3 class="font-bold text-gray-900 text-lg" id="modalDivisionName">Daftar Pegawai</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="overflow-y-auto p-6" id="modalBody"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const divisionData = @json($divisionData->values());

    function openModal(index, name) {
        const data = divisionData[index];
        if (!data) return;

        document.getElementById('modalDivisionName').textContent = 'Daftar Pegawai — ' + name;
        const body = document.getElementById('modalBody');
        body.innerHTML = '';

        const submitted = data.employees.filter(e => e.submission);
        const pending = data.employees.filter(e => !e.submission);

        function renderEmployee(item, isSubmitted) {
            const user = item.user;
            const initial = (user.full_name || user.name || '?').charAt(0).toUpperCase();
            const bg = isSubmitted ? 'from-green-500 to-emerald-600 bg-green-50/50 border-green-100' : 'from-gray-400 to-gray-500 border-gray-100';
            const statusHtml = isSubmitted
                ? `<div class="flex items-center gap-2"><span class="px-2.5 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Sudah</span><a href="${item.submission.link}" target="_blank" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition">Lihat Video</a></div>`
                : `<span class="px-2.5 py-0.5 bg-red-100 text-red-600 text-xs font-semibold rounded-full">Belum</span>`;

            return `<div class="flex items-center justify-between px-5 py-3 ${isSubmitted ? 'bg-green-50/50' : ''}" style="border-bottom:1px solid ${isSubmitted ? 'rgba(0,128,0,0.1)' : 'rgba(0,0,0,0.05)'}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 bg-gradient-to-br ${bg} rounded-lg flex items-center justify-center text-white font-bold text-xs shrink-0">${initial}</div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate">${user.full_name || user.name}</p>
                        <p class="text-xs text-gray-500 truncate">${user.email}</p>
                    </div>
                </div>
                <div class="shrink-0 ml-3">${statusHtml}</div>
            </div>`;
        }

        submitted.forEach(e => body.innerHTML += renderEmployee(e, true));
        pending.forEach(e => body.innerHTML += renderEmployee(e, false));

        document.getElementById('employeeModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('employeeModal').style.display = 'none';
    }

    document.getElementById('employeeModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endpush
@endsection
