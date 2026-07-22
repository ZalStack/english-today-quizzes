<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\VideoChallenge;
use App\Models\VideoSubmission;
use Illuminate\Http\Request;

class VideoChallengeController extends Controller
{
    public function index()
    {
        $challenges = VideoChallenge::where('is_active', true)
            ->withCount('submissions')
            ->latest()
            ->get();

        $mySubmissions = VideoSubmission::where('user_id', auth()->id())
            ->get()
            ->keyBy('challenge_id');

        return view('employee.video-challenges.index', compact('challenges', 'mySubmissions'));
    }

    public function submit(Request $request, VideoChallenge $videoChallenge)
    {
        if (!$videoChallenge->is_active) {
            return back()->with('error', 'Challenge ini sudah tidak aktif.');
        }

        $request->validate([
            'link' => 'required|string|max:500',
        ]);

        $existing = VideoSubmission::where('challenge_id', $videoChallenge->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            $existing->update([
                'link' => $request->link,
            ]);

            return redirect()->route('employee.video-challenges.index')
                ->with('success', 'Link video berhasil diperbarui.');
        }

        VideoSubmission::create([
            'challenge_id' => $videoChallenge->id,
            'user_id' => auth()->id(),
            'link' => $request->link,
        ]);

        return redirect()->route('employee.video-challenges.index')
            ->with('success', 'Link video berhasil dikumpulkan.');
    }
}
