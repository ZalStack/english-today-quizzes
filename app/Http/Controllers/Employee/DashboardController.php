<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DailyQuestClaim;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserPet;
use App\Models\UserQuizAttempt;
use App\Models\VideoSubmission;
use App\Services\PointService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $userPet = UserPet::where('user_id', $user->id)->with('pet')->first();

        $completedAttempts = UserQuizAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();

        $statistics = [
            'total_quizzes_taken' => $completedAttempts->count(),
            'average_score'       => $completedAttempts->avg('score') ?? 0,
            'highest_score'       => $completedAttempts->max('score') ?? 0,
            'total_score'         => (int) $completedAttempts->sum('score'),
            'passed'              => $completedAttempts->filter(fn ($a) => ($a->score ?? 0) >= 70)->count(),
        ];
        $statistics['pass_rate'] = $statistics['total_quizzes_taken'] > 0
            ? round(($statistics['passed'] / $statistics['total_quizzes_taken']) * 100)
            : 0;

        // ---- Engaging system: XP, level & learning streak ----
        $xp = max(0, (int) $statistics['total_score']);
        $levelSize = 500;
        $level = intdiv($xp, $levelSize) + 1;
        $xpIntoLevel = $xp % $levelSize;

        $attemptDays = $completedAttempts->pluck('completed_at')
            ->filter()
            ->map(fn ($d) => $d->toDateString())
            ->unique()
            ->sort()
            ->values();

        $streak = 0;
        if ($attemptDays->isNotEmpty()) {
            $cursor = \Illuminate\Support\Carbon::today();
            $last = \Illuminate\Support\Carbon::parse($attemptDays->last());

            // Streak only counts if the user was active today or yesterday
            if ($last->isSameDay($cursor) || $last->isSameDay($cursor->copy()->subDay())) {
                $set = $attemptDays->flip();
                while ($set->has($cursor->toDateString())) {
                    $streak++;
                    $cursor->subDay();
                }
            }
        }

        $bestStreak = 0;
        if ($attemptDays->count() > 0) {
            $run = 1;
            for ($i = 1; $i < $attemptDays->count(); $i++) {
                $prev = \Illuminate\Support\Carbon::parse($attemptDays[$i - 1]);
                $curr = \Illuminate\Support\Carbon::parse($attemptDays[$i]);
                $run = $prev->diffInDays($curr) === 1 ? $run + 1 : 1;
                $bestStreak = max($bestStreak, $run);
            }
            $bestStreak = max($bestStreak, 1);
        }

        // ---- Last 7 days activity (mini chart) ----
        $dayLabelsId = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $weeklyActivity = collect(range(6, 0))->map(function ($back) use ($completedAttempts, $dayLabelsId) {
            $day = \Illuminate\Support\Carbon::today()->subDays($back);

            return [
                'label' => $dayLabelsId[(int) $day->format('w')],
                'day'   => $day->toDateString(),
                'count' => $completedAttempts->filter(
                    fn ($a) => $a->completed_at && $a->completed_at->isSameDay($day)
                )->count(),
            ];
        });
        $weeklyMax = max(1, (int) $weeklyActivity->max('count'));

        // ---- Achievements / milestones ----
        $videoSubmissions = VideoSubmission::where('user_id', $user->id)->count();
        $hasPerfect = $completedAttempts->contains(fn ($a) => ($a->score ?? 0) >= 100);

        $achievements = [
            ['key' => 'first',    'icon' => '🎯', 'title' => 'Langkah Pertama', 'desc' => 'Selesaikan kuis pertamamu',   'earned' => $statistics['total_quizzes_taken'] >= 1],
            ['key' => 'streak3',  'icon' => '🔥', 'title' => 'Konsisten 3 Hari', 'desc' => 'Belajar 3 hari berturut-turut', 'earned' => max($streak, $bestStreak) >= 3],
            ['key' => 'perfect',  'icon' => '💯', 'title' => 'Nilai Sempurna',   'desc' => 'Raih skor 100%',              'earned' => $hasPerfect],
            ['key' => 'sharp',    'icon' => '🏆', 'title' => 'High Flyer',       'desc' => 'Rata-rata nilai ≥ 80%',        'earned' => $statistics['average_score'] >= 80],
            ['key' => 'marathon', 'icon' => '📚', 'title' => 'Quiz Marathon',    'desc' => 'Selesaikan 10 kuis',           'earned' => $statistics['total_quizzes_taken'] >= 10],
            ['key' => 'creator',  'icon' => '🎬', 'title' => 'Video Creator',    'desc' => 'Kumpulkan video challenge',    'earned' => $videoSubmissions >= 1],
        ];

        $earnedCount = collect($achievements)->where('earned', true)->count();

        $takenQuizIds = UserQuizAttempt::where('user_id', $user->id)->pluck('quiz_id');

        $availableQuizzes = Quiz::where('status', 'active')
            ->whereNotIn('id', $takenQuizIds)
            ->with('category')
            ->latest()
            ->take(6)
            ->get();

        $ongoingQuizzes = UserQuizAttempt::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->with('quiz')
            ->latest('started_at')
            ->get();

        $recentScores = UserQuizAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('quiz')
            ->latest('completed_at')
            ->take(5)
            ->get();

        $leaderboard = User::where('role', 'employee')
            ->withCount(['quizAttempts as completed_quizzes_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->withSum(['quizAttempts as total_score' => function ($q) {
                $q->where('status', 'completed');
            }], 'score')
            ->orderByDesc('total_score')
            ->get()
            ->filter(fn ($u) => ($u->completed_quizzes_count ?? 0) > 0)
            ->take(5)
            ->values();

        $currentUserRank = null;

        $allRanked = User::where('role', 'employee')
            ->withCount(['quizAttempts as completed_quizzes_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->withSum(['quizAttempts as total_score' => function ($q) {
                $q->where('status', 'completed');
            }], 'score')
            ->orderByDesc('total_score')
            ->get()
            ->filter(fn ($u) => ($u->completed_quizzes_count ?? 0) > 0)
            ->values();

        $position = $allRanked->search(fn ($u) => $u->id === $user->id);

        if ($position !== false) {
            $currentUserRank = [
                'rank'        => $position + 1,
                'total_score' => $allRanked[$position]->total_score ?? 0,
            ];
        }

        return view('employee.dashboard', compact(
            'statistics',
            'availableQuizzes',
            'ongoingQuizzes',
            'recentScores',
            'leaderboard',
            'currentUserRank'
        ) + [
            'userPet'        => $userPet,
            'xp'             => $xp,
            'level'          => $level,
            'levelSize'      => $levelSize,
            'xpIntoLevel'    => $xpIntoLevel,
            'levelProgress'  => round(($xpIntoLevel / $levelSize) * 100),
            'streak'         => $streak,
            'bestStreak'     => $bestStreak,
            'weeklyActivity' => $weeklyActivity,
            'weeklyMax'      => $weeklyMax,
            'achievements'   => $achievements,
            'earnedCount'    => $earnedCount,
            'points'         => (int) $user->points,
            'dailyQuests'    => $this->dailyQuests($user),
        ]);
    }

    /**
     * Definisi misi harian + status pengerjaan & klaim hari ini.
     */
    private function dailyQuests(User $user): array
    {
        $userPet = UserPet::where('user_id', $user->id)->first();

        $done = [
            'kuis_hari_ini' => UserQuizAttempt::where('user_id', $user->id)
                ->where('status', 'completed')
                ->where('completed_at', '>=', today())
                ->exists(),
            'video_hari_ini' => VideoSubmission::where('user_id', $user->id)
                ->where('created_at', '>=', today())
                ->exists(),
            'rawat_pet_hari_ini' => (bool) ($userPet?->last_fed_at?->isToday()),
        ];

        $claimed = DailyQuestClaim::where('user_id', $user->id)
            ->where('claimed_on', today()->toDateString())
            ->pluck('quest_key')
            ->all();

        $definitions = [
            ['key' => 'kuis_hari_ini', 'icon' => '🎯', 'title' => 'Kuis Hari Ini', 'desc' => 'Selesaikan 1 kuis hari ini', 'reward' => 25],
            ['key' => 'video_hari_ini', 'icon' => '🎬', 'title' => 'Video Challenge', 'desc' => 'Kumpulkan 1 video challenge hari ini', 'reward' => 40],
            ['key' => 'rawat_pet_hari_ini', 'icon' => '🍖', 'title' => 'Rawat Peliharaan', 'desc' => 'Beri makan peliharaanmu hari ini', 'reward' => 15],
        ];

        return array_map(fn ($quest) => $quest + [
            'done' => $done[$quest['key']] ?? false,
            'claimed' => in_array($quest['key'], $claimed, true),
        ], $definitions);
    }

    /**
     * Klaim hadiah misi harian (AJAX).
     */
    public function claimQuest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'quest_key' => 'required|string|in:kuis_hari_ini,video_hari_ini,rawat_pet_hari_ini',
        ]);

        $user = Auth::user();
        $quest = collect($this->dailyQuests($user))
            ->firstWhere('key', $validated['quest_key']);

        if (!$quest) {
            return response()->json(['success' => false, 'message' => 'Misi tidak dikenal.'], 422);
        }

        if ($quest['claimed']) {
            return response()->json(['success' => false, 'message' => 'Misi ini sudah diklaim hari ini ⏳'], 422);
        }

        if (!$quest['done']) {
            return response()->json(['success' => false, 'message' => 'Selesaikan dulu misinya sebelum klaim hadiah! 💪'], 422);
        }

        try {
            $pointService = app(PointService::class);
            $tx = $pointService->earn(
                $user,
                (int) $quest['reward'],
                'daily_quest',
                "Misi Harian '{$quest['title']}' selesai — +{$quest['reward']} poin ⭐"
            );

            DailyQuestClaim::create([
                'user_id' => $user->id,
                'quest_key' => $quest['key'],
                'claimed_on' => today()->toDateString(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to claim daily quest: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Gagal klaim hadiah, coba lagi.'], 500);
        }

        return response()->json([
            'success' => true,
            'reward' => (int) $quest['reward'],
            'balance' => (int) $tx->balance_after,
            'message' => "+{$quest['reward']} 🪙 poin masuk ke dompetmu!",
        ]);
    }
}
