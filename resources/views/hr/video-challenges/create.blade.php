@extends('layouts.app')

@section('title', 'Buat Challenge Baru')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4 sm:mb-6">
            <a href="{{ route('hr.video-challenges.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">&larr; Kembali</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-8">
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 mb-4 sm:mb-6">Buat Video Challenge Baru</h1>

            <form action="{{ route('hr.video-challenges.store') }}" method="POST">
                @csrf

                <div class="mb-4 sm:mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Judul Challenge</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm sm:text-base"
                           placeholder="Contoh: Introduce Myself, My Daily Activity, dll">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4 sm:mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Deskripsi <span class="text-gray-400">(opsional)</span></label>
                    <textarea name="description" rows="4"
                              class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm sm:text-base"
                              placeholder="Jelaskan challenge ini...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <button type="submit"
                            class="w-full sm:w-auto px-4 sm:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold text-sm sm:text-base">
                        Simpan Challenge
                    </button>
                    <a href="{{ route('hr.video-challenges.index') }}"
                       class="w-full sm:w-auto px-4 sm:px-8 py-2.5 sm:py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-semibold text-center text-sm sm:text-base">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
