@extends('layouts.hr')

@section('title', 'Upload Materi')
@section('header-title', 'Upload Materi')
@section('header-subtitle', 'Upload file materi untuk karyawan')

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('hr.materi.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
</div>

<form action="{{ route('hr.materi.store') }}" method="POST" enctype="multipart/form-data" id="materiForm">
    @csrf

    <div class="hr-card overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100/80">
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Detail Materi</h3>
            <p class="text-sm text-slate-500 mt-0.5">Informasi materi yang akan diupload</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">Judul Materi <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="hr-input" placeholder="e.g., Presentation Skills Materi">
                    @error('title') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi</label>
                    <textarea name="description" id="description" rows="4" class="hr-input" placeholder="Deskripsi singkat mengenai materi ini">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">File Materi <span class="text-red-500">*</span></label>
                    <div class="import-dropzone relative border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:border-primary-400 transition-all duration-300 cursor-pointer"
                         id="dropzone"
                         onclick="document.getElementById('file').click()">
                        <input type="file" name="file" id="file" class="hidden"
                               accept=".pdf,.ppt,.pptx"
                               onchange="handleFileSelect(this)">
                        <div id="dropzoneContent">
                            <div class="w-14 h-14 bg-primary-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-700 mb-1">Klik untuk upload file</p>
                            <p class="text-xs text-slate-400">Format: PDF, PPT, PPTX (Maks. 20MB)</p>
                        </div>
                        <div id="filePreview" class="hidden">
                            <div class="flex items-center justify-center gap-3">
                                <div id="fileIcon" class="w-12 h-12 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="text-left min-w-0">
                                    <p id="fileName" class="text-sm font-semibold text-slate-700 truncate"></p>
                                    <p id="fileSize" class="text-xs text-slate-400"></p>
                                </div>
                                <button type="button" onclick="removeFile()" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @error('file') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                               class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                        <span class="text-sm font-semibold text-slate-700">Aktif</span>
                        <span class="text-xs text-slate-400">&mdash; Materi akan terlihat oleh karyawan</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('hr.materi.index') }}" class="hr-btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Cancel
        </a>
        <button type="submit" class="hr-btn-primary" id="submitBtn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Upload Materi
        </button>
    </div>
</form>

@push('scripts')
<script>
    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const ext = file.name.split('.').pop().toLowerCase();

            document.getElementById('dropzoneContent').classList.add('hidden');
            document.getElementById('filePreview').classList.remove('hidden');
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = formatFileSize(file.size);

            const icon = document.getElementById('fileIcon');
            if (ext === 'pdf') {
                icon.className = 'w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-600';
            } else {
                icon.className = 'w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600';
            }
        }
    }

    function removeFile() {
        document.getElementById('file').value = '';
        document.getElementById('dropzoneContent').classList.remove('hidden');
        document.getElementById('filePreview').classList.add('hidden');
    }

    function formatFileSize(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return bytes + ' B';
    }

    document.getElementById('materiForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Uploading...';
    });
</script>
@endpush
@endsection
