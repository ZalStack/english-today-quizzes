{{-- resources/views/hr/video-challenges/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Challenge')

@section('content')
<div class="py-4 sm:py-6 lg:py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4 sm:mb-6">
            <a href="{{ route('hr.video-challenges.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">&larr; Kembali</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-8">
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 mb-4 sm:mb-6">Edit Challenge</h1>

            <form action="{{ route('hr.video-challenges.update', $videoChallenge) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4 sm:mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Judul Challenge</label>
                    <input type="text" name="title" value="{{ old('title', $videoChallenge->title) }}" required
                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm sm:text-base"
                           placeholder="Contoh: Introduce Myself, My Daily Activity, dll">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4 sm:mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Deskripsi <span class="text-gray-400">(opsional)</span></label>
                    <textarea name="description" rows="4"
                              class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm sm:text-base"
                              placeholder="Jelaskan challenge ini...">{{ old('description', $videoChallenge->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4 sm:mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Link Materi (Google Drive) <span class="text-gray-400">(opsional)</span></label>
                    <input type="url" name="material_link" value="{{ old('material_link', $videoChallenge->material_link) }}"
                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm sm:text-base"
                           placeholder="https://drive.google.com/file/d/...">
                    @error('material_link') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4 sm:mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Judul Materi <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" name="material_title" value="{{ old('material_title', $videoChallenge->material_title) }}"
                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm sm:text-base"
                           placeholder="Contoh: Materi Introduce Myself">
                    @error('material_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4 sm:mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Link Kisi-Kisi (Google Drive) <span class="text-gray-400">(opsional)</span></label>
                    <input type="url" name="kisi_kisi_link" value="{{ old('kisi_kisi_link', $videoChallenge->kisi_kisi_link) }}"
                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm sm:text-base"
                           placeholder="https://drive.google.com/file/d/...">
                    @error('kisi_kisi_link') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4 sm:mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Judul Kisi-Kisi <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" name="kisi_kisi_title" value="{{ old('kisi_kisi_title', $videoChallenge->kisi_kisi_title) }}"
                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition text-sm sm:text-base"
                           placeholder="Contoh: Kisi-Kisi Introduce Myself">
                    @error('kisi_kisi_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4 sm:mb-6">
                    <label class="flex items-center gap-2 sm:gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $videoChallenge->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 sm:w-5 sm:h-5 text-indigo-600 focus:ring-indigo-500 rounded">
                        <span class="text-sm font-semibold text-gray-700">Aktif</span>
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <button type="submit"
                            class="w-full sm:w-auto px-4 sm:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold text-sm sm:text-base">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('hr.video-challenges.index') }}"
                       class="w-full sm:w-auto px-4 sm:px-8 py-2.5 sm:py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-semibold text-center text-sm sm:text-base">
                        Batal
                    </a>
                </div>
            </form>

            <form action="{{ route('hr.video-challenges.destroy', $videoChallenge) }}" method="POST" class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Hapus challenge ini? Semua pengumpulan terkait akan ikut terhapus.')"
                        class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition font-semibold text-xs sm:text-sm">
                    Hapus Challenge
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
