<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VideoChallenge;
use App\Models\VideoSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VideoChallengeController extends Controller
{
    public function index()
    {
        try {
            // Cache active employees for 5 minutes
            $activeEmployees = Cache::remember('active_employees', 300, function() {
                return User::where('role', 'employee')
                    ->where('status', 'active')
                    ->with('division')
                    ->get();
            });

            $challenges = VideoChallenge::where('is_active', true)
                ->withCount('submissions')
                ->latest()
                ->get();

            $mySubmissions = VideoSubmission::where('user_id', auth()->id())
                ->get()
                ->keyBy('challenge_id');

            // Get submissions for all challenges efficiently
            $challengeIds = $challenges->pluck('id');

            // Use eager loading and cache
            $allSubmissions = VideoSubmission::whereIn('challenge_id', $challengeIds)
                ->with('user')
                ->get()
                ->groupBy('challenge_id');

            // Prepare data for each challenge
            $challengesData = [];
            foreach ($challenges as $challenge) {
                $submissions = $allSubmissions->get($challenge->id, collect());
                $submittedUserIds = $submissions->pluck('user_id')->flip();

                // Group by division
                $divisionStats = [];
                $grouped = $activeEmployees->groupBy(function($u) {
                    return $u->division ? $u->division->name : 'Tanpa Divisi';
                });

                foreach ($grouped as $divisionName => $emps) {
                    $submittedEmps = $emps->filter(function($u) use ($submittedUserIds) {
                        return $submittedUserIds->has($u->id);
                    });

                    $employeesData = [];
                    foreach ($emps as $user) {
                        $submission = $submissions->firstWhere('user_id', $user->id);
                        $employeesData[] = [
                            'id' => $user->id,
                            'name' => $user->full_name ?? $user->name ?? 'Unknown',
                            'email' => $user->email ?? '',
                            'submitted' => !is_null($submission),
                            'link' => $submission ? $submission->link : null,
                            'embed' => $submission ? \App\Helpers\VideoLinkHelper::embedUrl($submission->link) : null,
                            'is_me' => $user->id === auth()->id(),
                        ];
                    }

                    $divisionStats[] = [
                        'name' => $divisionName,
                        'total' => $emps->count(),
                        'submitted' => $submittedEmps->count(),
                        'employees' => $employeesData,
                    ];
                }

                // Sort divisions
                usort($divisionStats, function($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });

                $challengeData = [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'description' => $challenge->description,
                    'created_at' => $challenge->created_at->format('d M Y H:i'),
                    'is_active' => $challenge->is_active,
                    'division_stats' => $divisionStats,
                    'total_employees' => $activeEmployees->count(),
                    'total_submitted' => $submittedUserIds->count(),
                    'my_submission' => $mySubmissions->get($challenge->id),
                ];

                $challengesData[] = $challengeData;
            }

            return view('employee.video-challenges.index', [
                'challenges' => $challenges,
                'challengesData' => $challengesData,
                'mySubmissions' => $mySubmissions,
            ]);

        } catch (\Exception $e) {
            \Log::error('Video Challenge Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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

            // Clear cache
            Cache::forget('active_employees');

            return redirect()->route('employee.video-challenges.index')
                ->with('success', 'Link video berhasil diperbarui.');
        }

        VideoSubmission::create([
            'challenge_id' => $videoChallenge->id,
            'user_id' => auth()->id(),
            'link' => $request->link,
        ]);

        // Clear cache
        Cache::forget('active_employees');

        return redirect()->route('employee.video-challenges.index')
            ->with('success', 'Link video berhasil dikumpulkan.');
    }
}
