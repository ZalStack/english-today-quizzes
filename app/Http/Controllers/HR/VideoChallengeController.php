<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VideoChallenge;
use App\Models\VideoSubmission;
use Illuminate\Http\Request;

class VideoChallengeController extends Controller
{
    public function index()
    {
        $challenges = VideoChallenge::withCount('submissions')
            ->latest()
            ->paginate(12);

        return view('hr.video-challenges.index', compact('challenges'));
    }

    public function create()
    {
        return view('hr.video-challenges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        VideoChallenge::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('hr.video-challenges.index')
            ->with('success', 'Video Challenge created successfully.');
    }

    public function show(VideoChallenge $videoChallenge)
    {
        $priorityEmails = [
            'ridwan.saputra@etquizzes.com',
            'anis.kurniasih@etquizzes.com',
        ];

        $employees = User::where('role', 'employee')->where('status', 'active')
            ->with('division')
            ->get()
            ->sortBy(function ($u) use ($priorityEmails) {
                $pos = array_search($u->email, $priorityEmails);
                return $pos === false ? 999 : $pos;
            })->values();

        $submissions = VideoSubmission::where('challenge_id', $videoChallenge->id)
            ->with('user')
            ->get()
            ->keyBy('user_id');

        $stats = [
            'total_employees' => $employees->count(),
            'submitted' => 0,
            'pending' => 0,
        ];

        $grouped = $employees->groupBy(fn($u) => $u->division?->name ?? '__no_division');

        $allSubmitted = 0;
        $allPending = 0;

        $divisionData = $grouped->map(function ($emps, $divisionName) use ($submissions, &$stats, &$allSubmitted, &$allPending) {
            $submittedCount = $emps->filter(fn($u) => $submissions->has($u->id))->count();
            $pendingCount = $emps->count() - $submittedCount;

            $stats['submitted'] += $submittedCount;
            $stats['pending'] += $pendingCount;
            $allSubmitted += $submittedCount;
            $allPending += $pendingCount;

            return [
                'division_name' => $divisionName === '__no_division' ? 'Tanpa Divisi' : $divisionName,
                'is_no_division' => $divisionName === '__no_division',
                'employees' => $emps->map(function ($user) use ($submissions) {
                    return [
                        'user' => $user,
                        'submission' => $submissions->get($user->id),
                    ];
                }),
                'submitted_count' => $submittedCount,
                'pending_count' => $pendingCount,
                'total' => $emps->count(),
            ];
        })->sortByDesc(fn($d) => $d['is_no_division']);

        $divisionData->prepend([
            'division_name' => 'ALL',
            'is_no_division' => false,
            'employees' => $employees->map(function ($user) use ($submissions) {
                return [
                    'user' => $user,
                    'submission' => $submissions->get($user->id),
                ];
            }),
            'submitted_count' => $allSubmitted,
            'pending_count' => $allPending,
            'total' => $employees->count(),
        ]);

        return view('hr.video-challenges.show', compact('videoChallenge', 'divisionData', 'stats'));
    }

    public function edit(VideoChallenge $videoChallenge)
    {
        return view('hr.video-challenges.edit', compact('videoChallenge'));
    }

    public function update(Request $request, VideoChallenge $videoChallenge)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $videoChallenge->update([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('hr.video-challenges.index')
            ->with('success', 'Video Challenge updated successfully.');
    }

    public function destroy(VideoChallenge $videoChallenge)
    {
        $videoChallenge->delete();

        return redirect()->route('hr.video-challenges.index')
            ->with('success', 'Video Challenge deleted successfully.');
    }
}
