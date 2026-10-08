{{-- resources/views/employee/pet/index.blade.php --}}
@extends('layouts.app')

@section('title', '3D Virtual Pet Sanctuary')

@section('content')
@php
    $allPets = \App\Models\Pet::all();
    $logs = \App\Models\PetLog::where('user_id', auth()->id())->latest()->take(5)->get();
@endphp

<div class="py-6 sm:py-8 lg:py-10 bg-slate-950 min-h-[calc(100vh-65px)] text-white"
     x-data="petSanctuaryPage()"
     x-init="initPage()">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Breadcrumb & Hero Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center text-xs font-semibold text-indigo-400 hover:text-indigo-300 mb-2 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Dashboard Utama
                </a>
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight bg-gradient-to-r from-amber-300 via-yellow-200 to-cyan-300 bg-clip-text text-transparent">
                    🐾 3D Virtual Pet Sanctuary
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">
                    Tempat tinggal & pusat perawatan hewan pendamping belajarmu. Kumpulkan EXP dari tugas untuk menetaskan dan mengembangkannya!
                </p>
            </div>

            <!-- Header Badges -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="px-3 py-1.5 rounded-xl bg-indigo-500/15 border border-indigo-500/30 text-xs font-semibold text-indigo-300 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    WebGL 3D Active
                </span>
                <button type="button"
                        @click="toggleSound()"
                        class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 text-xs font-semibold text-white transition flex items-center gap-1.5">
                    <span x-text="soundOn ? '🔊 Audio ON' : '🔇 Audio OFF'">🔊 Audio ON</span>
                </button>
            </div>
        </div>

        <!-- Main Showcase & Control Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 mb-10">

            <!-- Left: Giant 3D Interactive Stage (8 cols) -->
            <div class="lg:col-span-8 space-y-4">
                <div class="bg-gradient-to-b from-indigo-950/60 via-slate-900 to-slate-950 rounded-3xl border border-indigo-500/30 p-5 sm:p-7 shadow-2xl relative overflow-hidden">

                    <!-- Ambient Glow -->
                    <div class="absolute -top-32 -left-32 w-80 h-80 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

                    <!-- Stage Top Header -->
                    <div class="flex items-center justify-between gap-3 mb-4 relative z-10">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-400 to-yellow-300 flex items-center justify-center text-2xl shadow-lg shadow-amber-500/30 flex-shrink-0">
                                <span x-text="petState.status === 'hatched' ? '🐾' : '🥚'">🥚</span>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight"
                                        x-text="petState.status === 'hatched' ? (petState.nickname || 'Virtual Pet') : 'Telur Pengetahuan 3D'">
                                        Telur Pengetahuan 3D
                                    </h2>
                                    <template x-if="petState.status === 'hatched' && petState.pet">
                                        <span class="px-2.5 py-1 text-xs font-black uppercase rounded-lg tracking-wider border leading-none"
                                              :class="{
                                                  'bg-emerald-500/20 text-emerald-300 border-emerald-400/40': petState.pet.rarity === 'common',
                                                  'bg-blue-500/20 text-blue-300 border-blue-400/40': petState.pet.rarity === 'rare',
                                                  'bg-purple-500/20 text-purple-300 border-purple-400/40': petState.pet.rarity === 'epic',
                                                  'bg-amber-500/20 text-amber-300 border-amber-400/40 animate-pulse shadow-lg shadow-amber-500/20': petState.pet.rarity === 'legendary'
                                              }"
                                              x-text="petState.pet.rarity">
                                        </span>
                                    </template>
                                </div>
                                <p class="text-xs sm:text-sm text-indigo-200/70 mt-0.5"
                                   x-text="petState.status === 'hatched' ? (petState.pet?.species + ' • Level ' + petState.level) : 'Tingkatkan progress untuk memecahkan telur!'">
                                    Tingkatkan progress untuk memecahkan telur!
                                </p>
                            </div>
                        </div>

                        <!-- Ready To Hatch Badge -->
                        <template x-if="petState.status === 'ready_to_hatch' || petState.growthPercentage >= 100">
                            <span class="px-3.5 py-1.5 rounded-full bg-gradient-to-r from-amber-400 to-yellow-300 text-slate-950 font-black text-xs shadow-lg shadow-amber-500/50 animate-bounce">
                                ⚡ SIAP MENETAS!
                            </span>
                        </template>
                    </div>

                    <!-- 3D WebGL Canvas -->
                    <div class="relative w-full h-80 sm:h-96 rounded-2xl bg-gradient-to-b from-indigo-950/30 to-slate-950/80 border border-indigo-500/20 overflow-hidden shadow-inner flex items-center justify-center cursor-grab active:cursor-grabbing mb-5">
                        <div id="sanctuary-3d-canvas" class="w-full h-full"></div>

                        <!-- Canvas Watermark / Hint -->
                        <div class="absolute bottom-3 left-3 px-3 py-1 rounded-lg bg-black/60 backdrop-blur-md border border-white/10 text-[11px] text-indigo-200/90 pointer-events-none flex items-center gap-1.5">
                            <span>👆 Geser untuk putar 360° • Klik objek untuk interaksi</span>
                        </div>
                    </div>

                    <!-- Interactive Action Toolbar -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 relative z-10">

                        <!-- Hatch / Wobble (If Egg) -->
                        <template x-if="petState.status !== 'hatched'">
                            <div class="col-span-2 sm:col-span-4">
                                <template x-if="petState.status === 'ready_to_hatch' || petState.growthPercentage >= 100">
                                    <button type="button"
                                            @click="triggerHatch()"
                                            :disabled="isProcessing"
                                            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-500 hover:from-amber-300 hover:to-yellow-400 text-slate-950 font-black text-sm tracking-wide shadow-xl shadow-amber-500/30 transition-all flex items-center justify-center gap-2 transform active:scale-95">
                                        <span class="text-xl">🔨</span>
                                        <span x-text="isProcessing ? 'MEMECAHKAN TELUR...' : 'PECAHKAN TELUR SEKARANG!'">PECAHKAN TELUR SEKARANG!</span>
                                    </button>
                                </template>
                                <template x-if="petState.status === 'egg' && petState.growthPercentage < 100">
                                    <button type="button"
                                            @click="triggerWobble()"
                                            class="w-full py-3 px-4 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-sm transition-all flex items-center justify-center gap-2 border border-white/10">
                                        <span>✨</span>
                                        <span>Ketuk & Goyangkan Telur 3D</span>
                                    </button>
                                </template>
                            </div>
                        </template>

                        <!-- Actions (If Hatched) -->
                        <template x-if="petState.status === 'hatched'">
                            <button type="button"
                                    @click="feedPet()"
                                    :disabled="isProcessing"
                                    class="py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold text-xs sm:text-sm shadow-lg shadow-emerald-900/30 transition-all flex items-center justify-center gap-2 transform active:scale-95">
                                <span>🍖</span>
                                <span>Beri Makan</span>
                            </button>
                        </template>

                        <template x-if="petState.status === 'hatched'">
                            <button type="button"
                                    @click="petCreature()"
                                    class="py-3 px-4 rounded-xl bg-gradient-to-r from-pink-600 to-rose-500 hover:from-pink-500 hover:to-rose-400 text-white font-bold text-xs sm:text-sm shadow-lg shadow-pink-900/30 transition-all flex items-center justify-center gap-2 transform active:scale-95">
                                <span>💖</span>
                                <span>Elus & Bermain</span>
                            </button>
                        </template>

                        <template x-if="petState.status === 'hatched'">
                            <a href="{{ route('employee.quizzes.join') }}"
                               class="py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-blue-900/30 transition-all flex items-center justify-center gap-2 transform active:scale-95 text-center">
                                <span>⚔️</span>
                                <span>Latih (Kuis)</span>
                            </a>
                        </template>

                        <template x-if="petState.status === 'hatched'">
                            <button type="button"
                                    @click="resetEgg()"
                                    class="py-3 px-4 rounded-xl bg-white/10 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 font-bold text-xs sm:text-sm border border-white/10 hover:border-rose-500/30 transition-all flex items-center justify-center gap-2">
                                <span>🥚</span>
                                <span>Telur Baru</span>
                            </button>
                        </template>
                    </div>

                </div>
            </div>

            <!-- Right: Stats, Attributes & History (4 cols) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Primary Attributes Card -->
                <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-5 sm:p-6 shadow-xl">
                    <h3 class="font-bold text-base text-white mb-4 flex items-center gap-2">
                        <span>📊</span> Status & Atribut Pet
                    </h3>

                    <!-- EGG GROWTH OR EXP BAR -->
                    <template x-if="petState.status !== 'hatched'">
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center justify-between text-xs font-semibold">
                                <span class="text-slate-400">Progress Inkubasi</span>
                                <span class="text-amber-300 font-mono font-bold" x-text="petState.growthPercentage + '%'"></span>
                            </div>
                            <div class="w-full h-3 bg-black/40 rounded-full overflow-hidden p-0.5 border border-white/10">
                                <div class="h-full bg-gradient-to-r from-amber-500 to-yellow-400 rounded-full transition-all duration-500"
                                     :style="`width: ${Math.min(100, petState.growthPercentage)}%`"></div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Selesaikan kuis (+25 EXP) atau kumpulkan video (+40 EXP) untuk menambah energi telur!</p>
                        </div>
                    </template>

                    <template x-if="petState.status === 'hatched' && petState.pet">
                        <div class="space-y-4">
                            <!-- Level & EXP -->
                            <div>
                                <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                    <span class="text-slate-300">Level EXP</span>
                                    <span class="text-cyan-300 font-mono text-[11px]" x-text="petState.experience + ' / ' + petState.maxExperience + ' XP'"></span>
                                </div>
                                <div class="w-full h-2.5 bg-black/40 rounded-full overflow-hidden border border-white/10">
                                    <div class="h-full bg-gradient-to-r from-cyan-500 to-blue-400 rounded-full transition-all duration-300"
                                         :style="`width: ${Math.min(100, petState.expPercentage)}%`"></div>
                                </div>
                            </div>

                            <!-- Attribute Meters -->
                            <div class="grid grid-cols-2 gap-2">
                                <div class="bg-black/30 rounded-xl p-3 border border-white/5">
                                    <span class="text-[11px] text-slate-400 block">Kebahagiaan</span>
                                    <span class="text-sm font-black text-pink-300" x-text="petState.happiness + '% ❤️'"></span>
                                </div>
                                <div class="bg-black/30 rounded-xl p-3 border border-white/5">
                                    <span class="text-[11px] text-slate-400 block">Energi</span>
                                    <span class="text-sm font-black text-yellow-300" x-text="petState.energy + '% ⚡'"></span>
                                </div>
                                <div class="bg-black/30 rounded-xl p-3 border border-white/5">
                                    <span class="text-[11px] text-slate-400 block">Base Power</span>
                                    <span class="text-sm font-black text-amber-300" x-text="(petState.pet?.base_power || 10) + ' ⚔️'"></span>
                                </div>
                                <div class="bg-black/30 rounded-xl p-3 border border-white/5">
                                    <span class="text-[11px] text-slate-400 block">Elemen</span>
                                    <span class="text-sm font-black text-cyan-300 capitalize" x-text="(petState.pet?.element_icon || '✨') + ' ' + (petState.pet?.element || 'Nature')"></span>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="p-3 rounded-xl bg-indigo-950/30 border border-indigo-500/20">
                                <p class="text-xs text-slate-300 italic leading-relaxed" x-text="petState.pet?.description"></p>
                            </div>
                        </div>
                    </template>

                    <!-- Quick Testing Toolbar -->
                    <div class="pt-4 mt-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                        <span>Tes Cepat:</span>
                        <div class="flex items-center gap-1.5">
                            <button type="button"
                                    @click="addDevPoints(25)"
                                    class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-indigo-200 text-xs font-mono transition">
                                +25 EXP
                            </button>
                            <button type="button"
                                    @click="addDevPoints(100)"
                                    class="px-2.5 py-1 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-xs font-mono border border-amber-500/30 transition font-semibold">
                                ⚡ Instant 100%
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Logs -->
                <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-5 sm:p-6 shadow-xl">
                    <h3 class="font-bold text-base text-white mb-3 flex items-center gap-2">
                        <span>📜</span> Riwayat Aktivitas & EXP
                    </h3>
                    <div class="space-y-2.5">
                        @forelse($logs as $log)
                            <div class="p-2.5 rounded-xl bg-black/20 border border-white/5 flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs text-slate-200 font-medium truncate">{{ $log->description }}</p>
                                    <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 text-xs font-bold font-mono whitespace-nowrap">
                                    +{{ $log->growth_gained }} EXP
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500 text-center py-3">Belum ada riwayat aktivitas.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

        <!-- Bestiary & Creature Collection -->
        <div class="mt-12 pt-8 border-t border-slate-800">
            <div class="flex items-center justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2">
                        <span>📖</span> Ensiklopedia Hewan (Bestiary)
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-400 mt-0.5">
                        Daftar seluruh spesies hewan legendaris yang bisa kamu dapatkan melalui penetasan telur.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($allPets as $pet)
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-indigo-500/40 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-xs font-black uppercase px-2 py-0.5 rounded-md border
                                    @if($pet->rarity === 'legendary') bg-amber-500/20 text-amber-300 border-amber-500/30
                                    @elseif($pet->rarity === 'epic') bg-purple-500/20 text-purple-300 border-purple-500/30
                                    @elseif($pet->rarity === 'rare') bg-blue-500/20 text-blue-300 border-blue-500/30
                                    @else bg-gray-500/20 text-gray-300 border-gray-500/30 @endif">
                                    {{ $pet->rarity }}
                                </span>
                                <span class="text-xs font-bold text-slate-400">
                                    {{ $pet->element_icon }} {{ ucfirst($pet->element) }}
                                </span>
                            </div>
                            <h4 class="font-bold text-base text-white">{{ $pet->species }}</h4>
                            <p class="text-xs text-indigo-300/80 font-medium mb-2">{{ $pet->name }}</p>
                            <p class="text-[11px] text-slate-400 line-clamp-2">{{ $pet->description }}</p>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
                            <span>Power: <b class="text-amber-300">{{ $pet->base_power }}</b></span>
                            <span class="text-indigo-400 font-mono capitalize">{{ $pet->model_preset }} 3D</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script src="{{ asset('js/pet-3d-game.js') }}"></script>
<script>
    function petSanctuaryPage() {
        return {
            game: null,
            soundOn: true,
            isProcessing: false,
            petState: {
                status: '{{ $userPet->status }}',
                growthPercentage: {{ $userPet->growth_percentage }},
                growthPoints: {{ $userPet->growth_points }},
                maxGrowthPoints: {{ $userPet->max_growth_points }},
                crack_stage: {{ $userPet->crack_stage }},
                level: {{ $userPet->level }},
                experience: {{ $userPet->experience }},
                maxExperience: {{ $userPet->max_experience }},
                expPercentage: {{ $userPet->exp_percentage }},
                happiness: {{ $userPet->happiness }},
                energy: {{ $userPet->energy }},
                nickname: '{{ $userPet->nickname ?? "My Egg" }}',
                pet: @json($userPet->pet),
            },

            initPage() {
                this.game = new Pet3DGame('sanctuary-3d-canvas', {
                    apiUrl: '{{ route("employee.pet.index") }}',
                    csrfToken: '{{ csrf_token() }}',
                    soundEnabled: this.soundOn,
                    onStateChange: (state) => {
                        this.petState = Object.assign({}, this.petState, state);
                    },
                    onHatched: (data) => {
                        this.isProcessing = false;
                    }
                });
            },

            toggleSound() {
                this.soundOn = !this.soundOn;
                if (this.game) {
                    this.game.soundEnabled = this.soundOn;
                }
            },

            triggerWobble() {
                if (this.game) this.game.triggerWobble();
            },

            triggerHatch() {
                if (this.game && !this.isProcessing) {
                    this.isProcessing = true;
                    this.game.triggerHatchSequence();
                    setTimeout(() => { this.isProcessing = false; }, 2000);
                }
            },

            async feedPet() {
                if (this.game && !this.isProcessing) {
                    this.isProcessing = true;
                    await this.game.feed();
                    this.isProcessing = false;
                }
            },

            petCreature() {
                if (this.game) this.game.petJumpAnimation();
            },

            async addDevPoints(amount) {
                if (this.game) {
                    await this.game.addPoints(amount);
                }
            },

            async resetEgg() {
                if (confirm('Apakah kamu ingin menetaskan telur baru? (Pet saat ini akan diganti dengan telur baru)')) {
                    if (this.game) {
                        await this.game.resetNewEgg();
                    }
                }
            }
        };
    }
</script>
@endpush
@endsection
