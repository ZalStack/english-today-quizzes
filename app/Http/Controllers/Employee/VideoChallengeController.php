<?php
// app/Http/Controllers/Employee/VideoChallengeController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VideoChallenge;
use App\Models\VideoSubmission;
use App\Helpers\VideoLinkHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VideoChallengeController extends Controller
{
    public function index()
    {
        try {
            // FIX: jangan cache Eloquent Collection mentah (rawan gagal unserialize
            // di beberapa environment/hosting). Cache sebagai array primitif saja,
            // lalu bangun ulang jadi collection setelah diambil.
            $activeEmployeesArray = Cache::remember('active_employees_v2', 300, function () {
                return User::where('role', 'employee')
                    ->where('status', 'active')
                    ->with('division')
                    ->get()
                    ->map(function ($u) {
                        return [
                            'id' => $u->id,
                            'full_name' => $u->full_name ?? null,
                            'name' => $u->name ?? null,
                            'email' => $u->email ?? '',
                            'division_name' => $u->division->name ?? 'Tanpa Divisi',
                        ];
                    })
                    ->toArray();
            });

            $activeEmployees = collect($activeEmployeesArray);

            $challenges = VideoChallenge::where('is_active', true)
                ->withCount('submissions')
                ->latest()
                ->get();

            $mySubmissions = VideoSubmission::where('user_id', auth()->id())
                ->get()
                ->keyBy('challenge_id');

            $challengeIds = $challenges->pluck('id');

            $allSubmissions = VideoSubmission::whereIn('challenge_id', $challengeIds)
                ->with('user')
                ->get()
                ->groupBy('challenge_id');

            $challengesData = [];
            foreach ($challenges as $challenge) {
                $submissions = $allSubmissions->get($challenge->id, collect());
                $submittedUserIds = $submissions->pluck('user_id')->flip();

                $grouped = $activeEmployees->groupBy('division_name');

                $divisionStats = [];
                foreach ($grouped as $divisionName => $emps) {
                    $submittedEmps = $emps->filter(function ($u) use ($submittedUserIds) {
                        return $submittedUserIds->has($u['id']);
                    });

                    $employeesData = [];
                    foreach ($emps as $user) {
                        $submission = $submissions->firstWhere('user_id', $user['id']);
                        $employeesData[] = [
                            'id' => $user['id'],
                            'name' => $user['full_name'] ?: ($user['name'] ?: 'Unknown'),
                            'email' => $user['email'],
                            'submitted' => !is_null($submission),
                            'link' => $submission ? $submission->link : null,
                            'embed' => $submission ? VideoLinkHelper::embedUrl($submission->link) : null,
                            'is_me' => $user['id'] === auth()->id(),
                        ];
                    }

                    $divisionStats[] = [
                        'name' => $divisionName,
                        'total' => $emps->count(),
                        'submitted' => $submittedEmps->count(),
                        'employees' => $employeesData,
                    ];
                }

                usort($divisionStats, function ($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });

                // Prepare material and kisi-kisi data
                $materialData = null;
                if ($challenge->material_link) {
                    $materialData = [
                        'link' => $challenge->material_link,
                        'title' => $challenge->material_title ?? 'Materi Video Challenge',
                        'embed' => VideoLinkHelper::getDriveEmbedUrl($challenge->material_link),
                        'is_drive' => VideoLinkHelper::isDriveLink($challenge->material_link),
                        'is_youtube' => VideoLinkHelper::isYoutubeLink($challenge->material_link),
                    ];
                }

                $kisiKisiData = null;
                if ($challenge->kisi_kisi_link) {
                    $kisiKisiData = [
                        'link' => $challenge->kisi_kisi_link,
                        'title' => $challenge->kisi_kisi_title ?? 'Kisi-Kisi Video Challenge',
                        'embed' => VideoLinkHelper::getDriveEmbedUrl($challenge->kisi_kisi_link),
                        'is_drive' => VideoLinkHelper::isDriveLink($challenge->kisi_kisi_link),
                        'is_youtube' => VideoLinkHelper::isYoutubeLink($challenge->kisi_kisi_link),
                    ];
                }

                $challengesData[] = [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'description' => $challenge->description,
                    'created_at' => $challenge->created_at->format('d M Y H:i'),
                    'is_active' => $challenge->is_active,
                    'division_stats' => $divisionStats,
                    'total_employees' => $activeEmployees->count(),
                    'total_submitted' => $submittedUserIds->count(),
                    'my_submission' => $mySubmissions->get($challenge->id),
                    'material' => $materialData,
                    'kisi_kisi' => $kisiKisiData,
                ];
            }

            return view('employee.video-challenges.index', [
                'challenges' => $challenges,
                'challengesData' => $challengesData,
                'mySubmissions' => $mySubmissions,
            ]);

        } catch (\Throwable $e) {
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
            $existing->update(['link' => $request->link]);
            Cache::forget('active_employees_v2');

            return redirect()->route('employee.video-challenges.index')
                ->with('success', 'Link video berhasil diperbarui.');
        }

        VideoSubmission::create([
            'challenge_id' => $videoChallenge->id,
            'user_id' => auth()->id(),
            'link' => $request->link,
        ]);

        Cache::forget('active_employees_v2');

        return redirect()->route('employee.video-challenges.index')
            ->with('success', 'Link video berhasil dikumpulkan.');
    }
}
