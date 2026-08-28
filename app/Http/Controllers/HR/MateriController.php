<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\NotificationHelper;

class MateriController extends Controller
{
    public function index()
    {
        $totalMateri = Materi::count();
        $activeMateri = Materi::where('is_active', true)->count();
        $pdfCount = Materi::where('file_type', 'pdf')->count();
        $pptCount = Materi::whereIn('file_type', ['ppt', 'pptx'])->count();

        $materi = Materi::with('uploader')->latest()->paginate(10);

        return view('hr.materi.index', compact('materi', 'totalMateri', 'activeMateri', 'pdfCount', 'pptCount'));
    }

    public function create()
    {
        return view('hr.materi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,ppt,pptx|max:20480',
            'is_active' => 'boolean',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        $validated['file_path'] = $file->store('materi', 'public');
        $validated['file_original_name'] = $file->getClientOriginalName();
        $validated['file_type'] = $extension;
        $validated['file_size'] = $file->getSize();
        $validated['uploaded_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);

        unset($validated['file']);

        $materi = Materi::create($validated);

        NotificationHelper::notifyMaterialUpload(auth()->user(), $materi);

        return redirect()->route('hr.materi.index')->with('success', 'Materi berhasil diupload.');
    }

    public function edit(Materi $materi)
    {
        return view('hr.materi.edit', compact('materi'));
    }

    public function update(Request $request, Materi $materi)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,ppt,pptx|max:20480',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('file')) {
            if ($materi->file_path) {
                Storage::disk('public')->delete($materi->file_path);
            }

            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());

            $validated['file_path'] = $file->store('materi', 'public');
            $validated['file_original_name'] = $file->getClientOriginalName();
            $validated['file_type'] = $extension;
            $validated['file_size'] = $file->getSize();
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        unset($validated['file']);

        $materi->update($validated);

        return redirect()->route('hr.materi.index')->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Materi $materi)
    {
        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        return redirect()->route('hr.materi.index')->with('success', 'Materi berhasil dihapus.');
    }

    public function download(Materi $materi)
    {
        $path = Storage::disk('public')->path($materi->file_path);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $materi->file_path,
            $materi->file_original_name
        );
    }
}
