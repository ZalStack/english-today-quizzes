@php
    $year = date('Y');
    $appName = config('app.name', 'English Today');
@endphp

<footer class="et-footer">
    <div class="et-footer__inner">
        {{-- Brand column --}}
        <div>
            <div class="et-footer__brand">
                <span class="et-brand__mark" style="width:38px;height:38px;">
                    <span class="text-[19px] leading-none" aria-hidden="true">🦉</span>
                </span>
                <span class="et-footer__title">English Today <span class="et-gradient-text">Quizzes</span></span>
            </div>
            <p class="et-footer__tagline">
                Platform belajar bahasa Inggris karyawan: kuis interaktif, video challenge,
                materi, dan teman virtual 3D — semuanya dalam satu tempat yang seru.
            </p>
            <div class="flex flex-wrap gap-2 mt-4">
                <span class="et-chip">📱 Responsif semua device</span>
                <span class="et-chip">🌙 Dark mode</span>
            </div>
        </div>

        {{-- Menu column --}}
        <div>
            <p class="et-footer__heading">Menu Utama</p>
            <a href="{{ route('employee.dashboard') }}" class="et-footer__link">📊 Dashboard</a>
            <a href="{{ route('employee.quizzes.join') }}" class="et-footer__link">🔑 Join Quiz</a>
            <a href="{{ route('employee.quizzes.history') }}" class="et-footer__link">📜 Riwayat Kuis</a>
            <a href="{{ route('employee.video-challenges.index') }}" class="et-footer__link">🎬 Video Challenge</a>
        </div>

        {{-- Resources column --}}
        <div>
            <p class="et-footer__heading">Sumber Belajar</p>
            <a href="{{ route('employee.materi.index') }}" class="et-footer__link">📚 Materi</a>
            <a href="{{ route('employee.pet.index') }}" class="et-footer__link">🐾 3D Pet Sanctuary</a>
            <a href="{{ route('notifications.index') }}" class="et-footer__link">🔔 Notifikasi</a>
            <a href="{{ route('employee.profile.edit') }}" class="et-footer__link">⚙️ Profil & Keamanan</a>
            <a href="https://sikekar.read1kpmseikhlasnya.com" target="_blank" rel="noopener noreferrer" class="et-footer__link">
                🚀 SIKEKAR <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    <div class="et-footer__bottom">
        <p>© {{ $year }} {{ $appName }}. Dibuat untuk karyawan — tetap konsisten belajar setiap hari.</p>
        <p class="flex items-center gap-1.5 justify-center md:justify-end">
            <span class="et-pulse-dot inline-block w-2 h-2 rounded-full bg-lime-500"></span>
            Sistem aktif & tersinkron otomatis
        </p>
    </div>
</footer>
