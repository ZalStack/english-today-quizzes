<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        // Get all active employees for each challenge
        $activeEmployees = User::where('role', 'employee')
            ->where('status', 'active')
            ->with('division')
            ->get();

        // Get submissions for all challenges
        $challengeIds = $challenges->pluck('id');
        $allSubmissions = VideoSubmission::whereIn('challenge_id', $challengeIds)
            ->with('user')
            ->get()
            ->groupBy('challenge_id');

        // Prepare data for each challenge
        $challenges->transform(function ($challenge) use ($activeEmployees, $allSubmissions) {
            $submissions = $allSubmissions->get($challenge->id, collect());
            $submittedUserIds = $submissions->pluck('user_id')->flip();

            // Group by division
            $divisionStats = $activeEmployees
                ->groupBy(fn($u) => $u->division?->name ?: 'Tanpa Divisi')
                ->map(function ($emps, $divisionName) use ($submittedUserIds, $submissions) {
                    $submittedEmps = $emps->filter(fn($u) => $submittedUserIds->has($u->id));

                    return [
                        'name' => $divisionName,
                        'total' => $emps->count(),
                        'submitted' => $submittedEmps->count(),
                        'employees' => $emps->map(function ($user) use ($submissions) {
                            return [
                                'user' => $user,
                                'submission' => $submissions->firstWhere('user_id', $user->id),
                            ];
                        })->values(),
                    ];
                })
                ->sortKeys()
                ->values();

            $challenge->division_stats = $divisionStats;
            $challenge->total_employees = $activeEmployees->count();
            $challenge->total_submitted = $submittedUserIds->count();

            return $challenge;
        });

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
