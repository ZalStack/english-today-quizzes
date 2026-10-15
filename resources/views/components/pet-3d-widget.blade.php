{{-- resources/views/components/pet-3d-widget.blade.php --}}
@props([
    'userPet' => null,
    'compact' => false,
])

@php
    $userPet = $userPet ?? app(\App\Services\PetService::class)->getOrCreateUserPet(auth()->user());
@endphp

<div class="bg-gradient-to-br from-[#0b2a3d] via-[#0f2042] to-slate-950 rounded-3xl shadow-xl border-2 border-sky-500/30 p-4 sm:p-5 text-white relative overflow-hidden mb-6"
     x-data="petGameWidget()"
     x-init="initWidget()">

    <!-- Ambient Glowing Background Lights -->
    <div class="absolute -top-16 -right-16 w-48 h-48 bg-cyan-500/15 rounded-full blur-2xl pointer-events-none"></div>
    <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-amber-500/15 rounded-full blur-2xl pointer-events-none"></div>

    <!-- Header Section -->
    <div class="flex items-center justify-between gap-2 mb-3 relative z-10">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-amber-400 to-yellow-300 flex items-center justify-center text-base shadow-md shadow-amber-500/30 flex-shrink-0">
                <span x-text="petState.status === 'hatched' ? '🐾' : '🥚'">🥚</span>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <h3 class="font-display font-bold text-sm sm:text-base text-white truncate tracking-tight"
                        x-text="petState.status === 'hatched' ? (petState.nickname || 'Virtual Pet') : 'Telur 3D'">
                        Telur 3D
                    </h3>
                    <template x-if="petState.status === 'hatched' && petState.pet">
                        <span class="px-1.5 py-0.5 text-[9px] font-black uppercase rounded tracking-wider border leading-none"
                              :class="{
                                  'bg-emerald-500/20 text-emerald-300 border-emerald-400/40': petState.pet.rarity === 'common',
                                  'bg-blue-500/20 text-blue-300 border-blue-400/40': petState.pet.rarity === 'rare',
                                  'bg-purple-500/20 text-purple-300 border-purple-400/40': petState.pet.rarity === 'epic',
                                  'bg-amber-500/20 text-amber-300 border-amber-400/40 animate-pulse': petState.pet.rarity === 'legendary'
                              }"
                              x-text="petState.pet.rarity">
                        </span>
                    </template>
                </div>
                <p class="text-[11px] text-sky-200/70 truncate"
                   x-text="petState.status === 'hatched' ? (petState.pet?.species + ' • Lv.' + petState.level) : 'Kumpulkan poin, beli alat, pecahkan telur!'">
                    Kumpulkan poin, beli alat, pecahkan telur!
                </p>
            </div>
        </div>

        <!-- Sound, Toko & Fullscreen Controls -->
        <div class="flex items-center gap-1 flex-shrink-0">
            <button type="button"
                    @click="toggleSound()"
                    class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-sky-200 hover:text-white transition-all text-xs"
                    :title="soundOn ? 'Mute' : 'Unmute'">
                <span x-text="soundOn ? '🔊' : '🔇'">🔊</span>
            </button>
            <a href="{{ route('employee.shop.index') }}"
               class="p-1.5 rounded-lg bg-amber-500/70 hover:bg-amber-500 text-white transition-all text-xs font-semibold flex items-center gap-1 border border-amber-300/40 et-press"
               title="🛒 Beli Alat & Makanan di Toko">
                🛒
            </a>
            <a href="{{ route('employee.pet.index') }}"
               class="p-1.5 rounded-lg bg-sky-600/70 hover:bg-sky-600 text-white transition-all text-xs font-semibold flex items-center gap-1 border border-sky-400/40 et-press"
               title="Buka Sanctuary Mode Penuh">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- 3D Interactive WebGL Canvas Container -->
    <div class="relative w-full h-56 sm:h-64 rounded-xl bg-gradient-to-b from-[#0b2a3d]/50 to-slate-950/70 border-2 border-sky-500/30 overflow-hidden shadow-inner flex items-center justify-center group cursor-grab active:cursor-grabbing mb-3.5">

        <!-- 3D Canvas Mount Point -->
        <div id="pet-3d-canvas-container" class="w-full h-full"></div>

        <!-- 3D Hint Badge -->
        <div class="absolute bottom-2 left-2 px-2 py-0.5 rounded bg-black/50 backdrop-blur-md border border-white/10 text-[10px] text-sky-200/80 pointer-events-none flex items-center gap-1">
            <span>👆 Putar 360° • Klik interaksi</span>
        </div>

        <!-- Ready to Hatch Floating Banner -->
        <template x-if="petState.status !== 'hatched' && (petState.status === 'ready_to_hatch' || petState.crack_percentage >= 100)">
            <div class="absolute top-2 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-400 text-slate-950 font-black text-[11px] shadow-lg shadow-amber-500/50 animate-bounce flex items-center gap-1 pointer-events-none whitespace-nowrap">
                <span>⚡ SIAP MENETAS!</span>
            </div>
        </template>
    </div>

    <!-- Stats & Action Controls -->
    <div class="space-y-3 relative z-10">

        <!-- EGG CRACK METER (If egg) -->
        <template x-if="petState.status !== 'hatched'">
            <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                    <span class="text-sky-200 text-[11px] flex items-center gap-1">
                        <span>🔨</span> Keretakan Telur
                    </span>
                    <span class="font-bold font-mono text-xs"
                          :class="petState.crack_percentage >= 75 ? 'text-amber-200 animate-pulse' : 'text-amber-300'"
                          x-text="petState.crack_percentage + '%'">0%</span>
                </div>

                <!-- Progress Bar Track -->
                <div class="w-full h-2.5 bg-black/40 rounded-full overflow-hidden p-0.5 border border-white/10 relative">
                    <div class="h-full rounded-full bg-gradient-to-r from-amber-500 via-yellow-400 to-orange-400 transition-all duration-500 relative"
                         :style="`width: ${Math.min(100, petState.crack_percentage)}%`"></div>
                </div>

                <p class="mt-1.5 text-[10.5px] text-sky-200/70 leading-snug">
                    Pukul telur pakai alat dari 🛒 Toko — kumpulkan poin dari kuis &amp; video challenge!
                </p>
            </div>
        </template>

        <!-- HATCHED PET STATS (If hatched) -->
        <template x-if="petState.status === 'hatched' && petState.pet">
            <div class="bg-white/5 rounded-xl p-3 border border-white/10 space-y-2">
                <!-- EXP Bar -->
                <div>
                    <div class="flex items-center justify-between text-[11px] mb-1">
                        <span class="text-lime-300">EXP Level Up</span>
                        <span class="text-cyan-300 font-mono text-[10px]" x-text="petState.experience + ' / ' + petState.maxExperience + ' XP'"></span>
                    </div>
                    <div class="w-full h-2 bg-black/40 rounded-full overflow-hidden border border-white/10">
                        <div class="h-full bg-gradient-to-r from-cyan-500 to-blue-400 rounded-full transition-all duration-300"
                             :style="`width: ${Math.min(100, petState.expPercentage)}%`"></div>
                    </div>
                </div>

                <!-- Happiness & Energy Meters -->
                <div class="grid grid-cols-2 gap-1.5">
                    <div class="bg-black/25 rounded-lg px-2 py-1.5 border border-white/5 flex items-center justify-between">
                        <span class="text-[10px] text-sky-300/80">Joy</span>
                        <span class="text-xs font-bold text-pink-300" x-text="petState.happiness + '% ❤️'"></span>
                    </div>
                    <div class="bg-black/25 rounded-lg px-2 py-1.5 border border-white/5 flex items-center justify-between">
                        <span class="text-[10px] text-sky-300/80">Energy</span>
                        <span class="text-xs font-bold text-yellow-300" x-text="petState.energy + '% ⚡'"></span>
                    </div>
                </div>
            </div>
        </template>

        <!-- ACTION BUTTONS -->
        <div>
            <!-- EGG HATCH BUTTON (When Ready) -->
            <template x-if="petState.status !== 'hatched' && (petState.status === 'ready_to_hatch' || petState.crack_percentage >= 100)">
                <button type="button"
                        @click="triggerHatch()"
                        :disabled="isProcessing"
                        class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-500 hover:from-amber-300 hover:to-yellow-400 text-slate-950 font-black text-xs tracking-wide shadow-lg shadow-amber-500/30 transition-all flex items-center justify-center gap-1.5 transform active:scale-95">
                    <span class="text-base">🔨</span>
                    <span x-text="isProcessing ? 'MEMECAHKAN...' : 'PECAHKAN TELUR SEKARANG!'">PECAHKAN TELUR SEKARANG!</span>
                </button>
            </template>

            <!-- EGG WOBBLE BUTTON (When not ready) -->
            <template x-if="petState.status === 'egg' && petState.crack_percentage < 100">
                <button type="button"
                        @click="triggerWobble()"
                        class="w-full py-2 px-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition-all flex items-center justify-center gap-1.5 border border-white/10">
                    <span>✨</span>
                    <span>Ketuk Telur 3D</span>
                </button>
            </template>

            <!-- HATCHED PET ACTIONS -->
            <template x-if="petState.status === 'hatched'">
                <div class="grid grid-cols-2 gap-1.5">
                    <button type="button"
                            @click="feedPet()"
                            :disabled="isProcessing"
                            class="py-2 px-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold text-xs shadow transition-all flex items-center justify-center gap-1">
                        <span>🍖</span>
                        <span>Beri Makan</span>
                    </button>
                    <button type="button"
                            @click="petCreature()"
                            class="py-2 px-2.5 rounded-xl bg-gradient-to-r from-pink-600 to-rose-500 hover:from-pink-500 hover:to-rose-400 text-white font-bold text-xs shadow transition-all flex items-center justify-center gap-1">
                        <span>💖</span>
                        <span>Elus Pet</span>
                    </button>
                </div>
            </template>
        </div>

        <!-- QUICK DEV / TEST TOOLBAR -->
        <div class="pt-1.5 border-t border-white/10 flex items-center justify-between text-[10px] text-sky-300/70">
            <span>Tes:</span>
            <div class="flex items-center gap-1">
                <button type="button"
                        @click="addDevPoints(25)"
                        class="px-1.5 py-0.5 rounded bg-white/10 hover:bg-white/20 text-sky-200 text-[10px] font-mono transition">
                    +25 EXP
                </button>
                <button type="button"
                        @click="addDevPoints(100)"
                        class="px-1.5 py-0.5 rounded bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-[10px] font-mono border border-amber-500/30 transition font-semibold">
                    ⚡ 100%
                </button>
                <template x-if="petState.status === 'hatched'">
                    <button type="button"
                            @click="resetEgg()"
                            class="px-1.5 py-0.5 rounded bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-[10px] font-mono border border-rose-500/30 transition">
                        🥚 Reset
                    </button>
                </template>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script src="{{ asset('js/pet-3d-game.js') }}"></script>
<script>
    function petGameWidget() {
        return {
            game: null,
            soundOn: true,
            isProcessing: false,
            petState: {
                status: '{{ $userPet->status }}',
                growthPercentage: {{ $userPet->growth_percentage }},
                growthPoints: {{ $userPet->growth_points }},
                maxGrowthPoints: {{ $userPet->max_growth_points }},
                crack_points: {{ (int) $userPet->crack_points }},
                crack_percentage: {{ (int) $userPet->crack_percentage }},
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

            initWidget() {
                this.game = new Pet3DGame('pet-3d-canvas-container', {
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
