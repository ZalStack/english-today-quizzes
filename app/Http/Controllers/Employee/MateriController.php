<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index()
    {
        $materi = Materi::where('is_active', true)
            ->with('uploader')
            ->latest()
            ->paginate(12);

        return view('employee.materi.index', compact('materi'));
    }

    public function show(Materi $materi)
    {
        if (!$materi->is_active) {
            return redirect()->route('employee.materi.index')->with('error', 'Materi ini sudah tidak tersedia.');
        }

        $materi->load('uploader');

        return view('employee.materi.show', compact('materi'));
    }

    public function download(Materi $materi)
    {
        if (!$materi->is_active) {
            return redirect()->route('employee.materi.index')->with('error', 'Materi ini sudah tidak tersedia.');
        }

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
