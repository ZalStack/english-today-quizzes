<?php
// app/Http/Controllers/HR/VideoChallengeController.php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VideoChallenge;
use App\Models\VideoSubmission;
use App\Helpers\VideoLinkHelper;
use Illuminate\Http\Request;

class VideoChallengeController extends Controller
{
    public function index()
    {
        $totalChallenges = VideoChallenge::count();
        $totalSubmissions = VideoSubmission::count();
        $activeChallenges = VideoChallenge::where('is_active', true)->count();

        $challenges = VideoChallenge::withCount('submissions')->latest()->paginate(9);

        // Get all divisions with employee counts
        $divisions = \App\Models\Division::withCount('users')->get();

        // Get active employees grouped by division
        $employees = User::where('role', 'employee')->where('status', 'active')->with('division')->get();

        $employeesByDivision = $employees->groupBy(fn($u) => $u->division?->name ?: 'Tanpa Divisi')->sortKeys();

        $totalEmployees = $employees->count();

        $challengeIds = $challenges->pluck('id');

        $submissionsByChallenge = VideoSubmission::whereIn('challenge_id', $challengeIds)->get()->groupBy('challenge_id')->map(fn($subs) => $subs->pluck('user_id')->flip());

        $challenges->getCollection()->transform(function ($challenge) use ($employeesByDivision, $totalEmployees, $submissionsByChallenge, $divisions) {
            $submittedIds = $submissionsByChallenge->get($challenge->id, collect());

            $breakdown = $employeesByDivision
                ->map(function ($emps, $divisionName) use ($submittedIds) {
                    $submitted = $emps->filter(fn($u) => $submittedIds->has($u->id))->count();

                    return [
                        'name' => $divisionName,
                        'submitted' => $submitted,
                        'total' => $emps->count(),
                    ];
                })
                ->values();

            $challenge->division_breakdown = $breakdown;
            $challenge->total_employees_snapshot = $totalEmployees;
            $challenge->total_submitted_snapshot = $breakdown->sum('submitted');
            $challenge->divisions = $divisions;

            return $challenge;
        });

        return view('hr.video-challenges.index', compact('challenges', 'totalChallenges', 'totalSubmissions', 'activeChallenges'));
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
            'material_link' => 'nullable|string|max:500',
            'material_title' => 'nullable|string|max:255',
            'kisi_kisi_link' => 'nullable|string|max:500',
            'kisi_kisi_title' => 'nullable|string|max:255',
        ]);

        VideoChallenge::create([
            'title' => $request->title,
            'description' => $request->description,
            'material_link' => $request->material_link,
            'material_title' => $request->material_title,
            'kisi_kisi_link' => $request->kisi_kisi_link,
            'kisi_kisi_title' => $request->kisi_kisi_title,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('hr.video-challenges.index')->with('success', 'Video Challenge created successfully.');
    }

    public function show(VideoChallenge $videoChallenge)
    {
        $priorityEmails = ['ridwan.saputra@etquizzes.com', 'anis.kurniasih@etquizzes.com'];

        $employees = User::where('role', 'employee')
            ->where('status', 'active')
            ->with('division')
            ->get()
            ->sortBy(function ($u) use ($priorityEmails) {
                $pos = array_search($u->email, $priorityEmails);
                return $pos === false ? 999 : $pos;
            })
            ->values();

        $submissions = VideoSubmission::where('challenge_id', $videoChallenge->id)->with('user')->get()->keyBy('user_id');

        $stats = [
            'total_employees' => $employees->count(),
            'submitted' => 0,
            'pending' => 0,
        ];

        $grouped = $employees->groupBy(fn($u) => $u->division?->name ?? '__no_division');

        $allSubmitted = 0;
        $allPending = 0;

        $divisionData = $grouped
            ->map(function ($emps, $divisionName) use ($submissions, &$stats, &$allSubmitted, &$allPending) {
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
            })
            ->sortByDesc(fn($d) => $d['is_no_division']);

        // Add ALL division data
        $allData = [
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
        ];

        // Prepend ALL data
        $divisionData = collect([$allData])->concat($divisionData);

        // Pre-process data for JavaScript
        $divisionDataJson = $divisionData
            ->map(function ($d) {
                return [
                    'name' => $d['division_name'],
                    'employees' => $d['employees']
                        ->map(function ($e) {
                            return [
                                'name' => $e['user']->full_name ?? $e['user']->name,
                                'email' => $e['user']->email,
                                'submitted' => $e['submission'] ? true : false,
                                'link' => $e['submission'] ? $e['submission']->link : null,
                                'embed' => $e['submission'] ? VideoLinkHelper::embedUrl($e['submission']->link) : null,
                            ];
                        })
                        ->values()
                        ->toArray(),
                ];
            })
            ->values()
            ->toArray();

        // Prepare material and kisi-kisi data
        $materialData = null;
        if ($videoChallenge->material_link) {
            $materialData = [
                'link' => $videoChallenge->material_link,
                'title' => $videoChallenge->material_title ?? 'Materi Video Challenge',
                'embed' => VideoLinkHelper::getDriveEmbedUrl($videoChallenge->material_link),
                'is_drive' => VideoLinkHelper::isDriveLink($videoChallenge->material_link),
                'is_youtube' => VideoLinkHelper::isYoutubeLink($videoChallenge->material_link),
            ];
        }

        $kisiKisiData = null;
        if ($videoChallenge->kisi_kisi_link) {
            $kisiKisiData = [
                'link' => $videoChallenge->kisi_kisi_link,
                'title' => $videoChallenge->kisi_kisi_title ?? 'Kisi-Kisi Video Challenge',
                'embed' => VideoLinkHelper::getDriveEmbedUrl($videoChallenge->kisi_kisi_link),
                'is_drive' => VideoLinkHelper::isDriveLink($videoChallenge->kisi_kisi_link),
                'is_youtube' => VideoLinkHelper::isYoutubeLink($videoChallenge->kisi_kisi_link),
            ];
        }

        return view('hr.video-challenges.show', compact('videoChallenge', 'divisionData', 'stats', 'divisionDataJson', 'materialData', 'kisiKisiData'));
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
            'material_link' => 'nullable|string|max:500',
            'material_title' => 'nullable|string|max:255',
            'kisi_kisi_link' => 'nullable|string|max:500',
            'kisi_kisi_title' => 'nullable|string|max:255',
        ]);

        $videoChallenge->update([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
            'material_link' => $request->material_link,
            'material_title' => $request->material_title,
            'kisi_kisi_link' => $request->kisi_kisi_link,
            'kisi_kisi_title' => $request->kisi_kisi_title,
        ]);

        return redirect()->route('hr.video-challenges.index')->with('success', 'Video Challenge updated successfully.');
    }

    public function destroy(VideoChallenge $videoChallenge)
    {
        $videoChallenge->delete();

        return redirect()->route('hr.video-challenges.index')->with('success', 'Video Challenge deleted successfully.');
    }

    public function exportSubmissions(VideoChallenge $videoChallenge)
    {
        $videoChallenge->load('submissions.user.division');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $videoChallenge->title . '_submissions.csv"',
        ];

        $callback = function () use ($videoChallenge) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['No', 'Nama', 'Email', 'Divisi', 'Link Video', 'Status', 'Tanggal Submit']);

            $no = 1;
            $allEmployees = \App\Models\User::where('role', 'employee')
                ->where('status', 'active')
                ->with('division')
                ->get();

            foreach ($allEmployees as $employee) {
                $submission = $videoChallenge->submissions->firstWhere('user_id', $employee->id);

                fputcsv($file, [
                    $no++,
                    $employee->full_name ?? $employee->name,
                    $employee->email,
                    $employee->division?->name ?? 'Tanpa Divisi',
                    $submission ? $submission->link : '-',
                    $submission ? 'Sudah Submit' : 'Belum Submit',
                    $submission ? $submission->created_at->format('d M Y H:i') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
