@extends('layouts.app')

@section('title', 'Edit Challenge')

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('hr.video-challenges.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">&larr; Kembali</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h1 class="text-2xl font-extrabold text-gray-900 mb-6">Edit Challenge</h1>

            <form action="{{ route('hr.video-challenges.update', $videoChallenge) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Challenge</label>
                    <input type="text" name="title" value="{{ old('title', $videoChallenge->title) }}" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                           placeholder="Contoh: Introduce Myself, My Daily Activity, dll">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi <span class="text-gray-400">(opsional)</span></label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                              placeholder="Jelaskan challenge ini...">{{ old('description', $videoChallenge->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $videoChallenge->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 rounded">
                        <span class="text-sm font-semibold text-gray-700">Aktif</span>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('hr.video-challenges.index') }}"
                       class="px-8 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-semibold">
                        Batal
                    </a>
                </div>
            </form>

            <form action="{{ route('hr.video-challenges.destroy', $videoChallenge) }}" method="POST" class="mt-8 pt-6 border-t border-gray-200">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Hapus challenge ini? Semua pengumpulan terkait akan ikut terhapus.')"
                        class="px-6 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition font-semibold text-sm">
                    Hapus Challenge
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
