<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Models\ShopItem;
use App\Models\User;
use App\Models\UserPet;
use App\Models\UserQuizAttempt;
use App\Models\VideoChallenge;
use App\Models\VideoSubmission;
use App\Services\PetService;
use Database\Seeders\PetSeeder;
use Database\Seeders\ShopItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Uji end-to-end loop ekonomi "case real":
 * wajib kuis + video challenge dulu → poin → beli alat → pecah telur →
 * rawat bayi → dewasa → bertelur (tier & generasi naik) → siklus berulang.
 */
class PetShopLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PetSeeder::class);
        $this->seed(ShopItemSeeder::class);
    }

    protected function employee(): User
    {
        return User::factory()->create(['role' => 'employee']);
    }

    /**
     * Buka gerbang "case real": 1 kuis selesai + 1 video challenge terkumpul.
     */
    protected function unlockEmployee(User $user): void
    {
        $hr = User::factory()->create(['role' => 'hr']);
        $category = QuizCategory::create(['name' => 'Umum '.uniqid()]);
        $quiz = Quiz::create([
            'title' => 'Kuis Pembuka',
            'category_id' => $category->id,
            'created_by' => $hr->id,
            'duration' => 10,
            'status' => 'active',
        ]);

        UserQuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'started_at' => now(),
            'completed_at' => now(),
            'score' => 80,
            'total_correct' => 1,
            'total_wrong' => 0,
            'status' => 'completed',
        ]);

        $challenge = VideoChallenge::create([
            'title' => 'Challenge Pembuka',
            'description' => 'Perkenalan diri',
            'is_active' => true,
            'created_by' => $hr->id,
        ]);

        VideoSubmission::create([
            'challenge_id' => $challenge->id,
            'user_id' => $user->id,
            'link' => 'https://youtube.com/watch?v=pembuka',
        ]);
    }

    /* ====================================================================
     * GERBANG "CASE REAL" — kuis + video dulu, baru Sanctuary & Toko
     * ==================================================================== */

    public function test_locked_gate_blocks_pet_and_shop_until_quiz_and_video_done(): void
    {
        $user = $this->employee();

        // Halaman gerbang tampil, bukan game sanctuary
        $this->actingAs($user)
            ->get('/employee/pet')
            ->assertOk()
            ->assertSee('Sanctuary-mu Terkunci')
            ->assertSee('Kerjakan 1 Kuis')
            ->assertSee('Kumpulkan 1 Video Challenge');

        // Status gerbang: dua-duanya belum
        $this->actingAs($user)
            ->getJson('/employee/pet/unlock-status')
            ->assertOk()
            ->assertJsonPath('done_quiz', false)
            ->assertJsonPath('done_video', false)
            ->assertJsonPath('unlocked', false);

        // Semua aksi pet & toko diblokir (JSON → 403)
        $this->actingAs($user)->getJson('/employee/pet/status')->assertStatus(403)->assertJsonPath('locked', true);
        $this->actingAs($user)->postJson('/employee/pet/crack')->assertStatus(403);
        $this->actingAs($user)->postJson('/employee/pet/hatch')->assertStatus(403);
        $this->actingAs($user)->postJson('/employee/pet/feed')->assertStatus(403);
        $this->actingAs($user)->postJson('/employee/pet/breed')->assertStatus(403);
        $this->actingAs($user)->postJson('/employee/shop/buy', ['item_id' => 1])->assertStatus(403);

        // Link web (navbar) → redirect ke gerbang
        $this->actingAs($user)->get('/employee/shop')->assertRedirect(route('employee.pet.index'));

        // Tidak ada poin gratis & tidak ada baris peliharaan selama terkunci
        $this->assertSame(0, (int) $user->fresh()->points);
        $this->assertSame(0, UserPet::where('user_id', $user->id)->count());

        // Quest "Rawat Peliharaan" belum muncul di dashboard selama terkunci
        $this->actingAs($user)
            ->get('/employee/dashboard')
            ->assertOk()
            ->assertDontSee('Rawat Peliharaan');
    }

    public function test_unlocks_after_one_quiz_and_one_video_without_freebies(): void
    {
        $user = $this->employee();
        $this->unlockEmployee($user);

        $this->actingAs($user)
            ->getJson('/employee/pet/unlock-status')
            ->assertOk()
            ->assertJsonPath('done_quiz', true)
            ->assertJsonPath('done_video', true)
            ->assertJsonPath('unlocked', true);

        // Sanctuary & toko terbuka penuh
        $this->actingAs($user)
            ->get('/employee/pet')
            ->assertOk()
            ->assertDontSee('Sanctuary-mu Terkunci')
            ->assertSee('Pet Sanctuary 3D');
        $this->actingAs($user)->get('/employee/shop')->assertOk();
        $this->actingAs($user)->getJson('/employee/pet/status')->assertOk();

        // Welcome pack DIHAPUS: semua harus diraih sendiri dari kuis & video
        $this->assertSame(0, (int) $user->fresh()->points);

        // Quest rawat pet muncul setelah gerbang terbuka
        $this->actingAs($user)
            ->get('/employee/dashboard')
            ->assertOk()
            ->assertSee('Rawat Peliharaan');
    }

    public function test_gate_unlocks_via_real_quiz_and_video_http_flows(): void
    {
        $user = $this->employee();
        $this->actingAs($user);

        // Siapkan kuis aktif + challenge video (tanpa data unlock buatan)
        $hr = User::factory()->create(['role' => 'hr']);
        $category = QuizCategory::create(['name' => 'Gerbang '.uniqid()]);
        $quiz = Quiz::create([
            'title' => 'Kuis Gerbang',
            'category_id' => $category->id,
            'created_by' => $hr->id,
            'duration' => 10,
            'status' => 'active',
        ]);
        foreach ([['apple', 1], ['banana', 2]] as [$answer, $order]) {
            Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => "Apa jawaban {$order}?",
                'question_type' => 'short_answer',
                'correct_answer' => $answer,
                'points' => 5,
                'order_number' => $order,
            ]);
        }
        $challenge = VideoChallenge::create([
            'title' => 'Video Pembuka',
            'description' => 'Perkenalan diri',
            'is_active' => true,
            'created_by' => $hr->id,
        ]);

        // Awal: terkunci total
        $this->getJson('/employee/pet/unlock-status')->assertJsonPath('unlocked', false);
        $this->getJson('/employee/pet/status')->assertStatus(403);

        // 1) Selesaikan kuis sungguhan lewat HTTP
        $this->post("/employee/quizzes/{$quiz->id}/start")->assertRedirect();
        $attempt = UserQuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->latest('id')->first();
        $this->post("/employee/quizzes/take/{$attempt->id}/save", ['question_id' => $quiz->questions[0]->id, 'answer' => 'apple']);
        $this->post("/employee/quizzes/take/{$attempt->id}/save", ['question_id' => $quiz->questions[1]->id, 'answer' => 'banana']);
        $this->post("/employee/quizzes/take/{$attempt->id}/submit")->assertRedirectContains('result');

        // Kuis saja BELUM membuka (syarat = kuis DAN video)
        $this->getJson('/employee/pet/unlock-status')
            ->assertJsonPath('done_quiz', true)
            ->assertJsonPath('done_video', false)
            ->assertJsonPath('unlocked', false);
        $this->getJson('/employee/pet/status')->assertStatus(403);
        $this->get('/employee/shop')->assertRedirect(route('employee.pet.index'));

        // 2) Kirim video challenge sungguhan lewat HTTP
        $this->post("/employee/video-challenges/{$challenge->id}/submit", ['link' => 'https://youtu.be/gerbang'])
            ->assertRedirect();

        // Dua misi selesai → gerbang terbuka penuh
        $this->getJson('/employee/pet/unlock-status')
            ->assertJsonPath('done_quiz', true)
            ->assertJsonPath('done_video', true)
            ->assertJsonPath('unlocked', true);
        $this->getJson('/employee/pet/status')->assertOk();
        $this->get('/employee/pet')->assertOk()->assertDontSee('Sanctuary-mu Terkunci');
        $this->get('/employee/shop')->assertOk();

        // Poin = hasil kuis (35) + video (40) — murni usaha sendiri, bukan paket gratisan
        $this->assertSame(75, (int) $user->fresh()->points);
    }

    public function test_all_employee_pages_render(): void
    {
        $user = $this->employee();

        // Terkunci: halaman umum jalan, pet = gerbang, shop = redirect
        $this->actingAs($user)->get('/employee/pet')->assertOk();
        $this->actingAs($user)->get('/employee/shop')->assertRedirect(route('employee.pet.index'));
        foreach ([
            '/employee/dashboard',
            '/employee/quizzes/join',
            '/employee/video-challenges',
            '/employee/materi',
            '/employee/profile',
        ] as $uri) {
            $this->actingAs($user)->get($uri)->assertOk();
        }

        // Terbuka: seluruh halaman penuh dirender
        $this->unlockEmployee($user);
        $user = $user->fresh();
        $this->actingAs($user);

        $this->get('/employee/pet')->assertOk()->assertSee('Pet Sanctuary 3D');
        $this->get('/employee/shop')->assertOk();
        $this->get('/employee/dashboard')->assertOk();
    }

    /* ====================================================================
     * EKONOMI POIN & TOKO
     * ==================================================================== */

    public function test_buy_requires_enough_points_then_succeeds(): void
    {
        $user = $this->employee();
        $this->unlockEmployee($user);
        $turbo = ShopItem::where('slug', 'bor-turbo')->first();

        // Saldo 0 (semua harus dikumpulkan sendiri) → gagal
        $this->actingAs($user)->postJson('/employee/shop/buy', ['item_id' => $turbo->id])
            ->assertJsonPath('success', false);

        // Kasih saldo lalu beli → sukses
        $user->update(['points' => 200]);
        $this->actingAs($user->fresh())->postJson('/employee/shop/buy', ['item_id' => $turbo->id])
            ->assertJsonPath('success', true)
            ->assertJsonPath('balance', 40);

        $this->assertSame(40, (int) $user->fresh()->points);
        $this->assertSame(
            (int) $turbo->uses,
            (int) $user->userItems()->where('shop_item_id', $turbo->id)->first()->quantity
        );
    }

    /* ====================================================================
     * SIKLUS PENUH: kuis → poin → alat → telur → rawat → bertelur
     * ==================================================================== */

    public function test_full_case_real_lifecycle_quiz_video_to_breed(): void
    {
        $user = $this->employee();
        $this->actingAs($user);
        $this->unlockEmployee($user);

        // Kerjakan kuis sungguhan lewat HTTP → poin masuk (kuis pertama, skor 80)
        $hr = User::factory()->create(['role' => 'hr']);
        $category = QuizCategory::create(['name' => 'Live '.uniqid()]);
        $quiz = Quiz::create([
            'title' => 'Kuis Case Real',
            'category_id' => $category->id,
            'created_by' => $hr->id,
            'duration' => 10,
            'status' => 'active',
            'enroll_key' => 'BUKA',
        ]);
        foreach ([['apple', 1], ['banana', 2]] as [$answer, $order]) {
            Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => "Apa jawaban {$order}?",
                'question_type' => 'short_answer',
                'correct_answer' => $answer,
                'points' => 5,
                'order_number' => $order,
            ]);
        }

        $this->actingAs($user->fresh());
        $this->post("/employee/quizzes/{$quiz->id}/start")->assertRedirect();
        $attempt = UserQuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->latest('id')->first();
        $this->post("/employee/quizzes/take/{$attempt->id}/save", ['question_id' => $quiz->questions[0]->id, 'answer' => 'apple']);
        $this->post("/employee/quizzes/take/{$attempt->id}/save", ['question_id' => $quiz->questions[1]->id, 'answer' => 'banana']);
        $this->post("/employee/quizzes/take/{$attempt->id}/submit")
            ->assertRedirectContains('result')
            ->assertSessionHas('quiz_points', 35);

        // 20 dasar + 15 bonus skor ≥80 — bukan poin gratisan
        $this->assertSame(35, (int) $user->fresh()->points);

        // Tambah poin (simulasi kumpulan kuis & video lain) lalu beli Bor Turbo
        $user->update(['points' => 1200]);
        $turbo = ShopItem::where('slug', 'bor-turbo')->first();
        $this->actingAs($user->fresh())->postJson('/employee/shop/buy', ['item_id' => $turbo->id])
            ->assertJsonPath('success', true);

        // Pukul telur 2× (52 + 52 = 104 ≥ 100) → siap menetas
        $this->actingAs($user->fresh())->postJson('/employee/pet/use-tool', ['item_id' => $turbo->id])
            ->assertJsonPath('success', true);
        $this->actingAs($user->fresh())->postJson('/employee/pet/use-tool', ['item_id' => $turbo->id])
            ->assertJsonPath('ready_to_hatch', true);

        // Menetas → Bayi (telur generasi 1, tier Perunggu)
        $this->actingAs($user->fresh())->postJson('/employee/pet/hatch', ['nickname' => 'Kwek'])
            ->assertJsonPath('success', true)
            ->assertJsonPath('life_stage', 'baby');
        $pet = UserPet::where('user_id', $user->id)->first();
        $this->assertSame('baby', $pet->life_stage);
        $this->assertNotNull($pet->pet_id);
        $this->assertSame(1, (int) $pet->generation);
        $this->assertSame('bronze', $pet->egg_tier);

        // Rawat hingga Dewasa dengan pakan premium
        $premium = ShopItem::where('slug', 'pakan-premium')->first();
        for ($i = 0; $i < 6; $i++) {
            $this->actingAs($user->fresh())->postJson('/employee/shop/buy', ['item_id' => $premium->id])
                ->assertJsonPath('success', true);
        }

        $stageUps = 0;
        for ($i = 0; $i < 10 && $pet->fresh()->life_stage !== 'adult'; $i++) {
            $res = $this->actingAs($user->fresh())->postJson('/employee/pet/feed')->assertOk();
            $res->assertJsonPath('success', true);
            if ($res->json('stage_up')) {
                $stageUps++;
            }
        }
        $pet->refresh();
        $this->assertSame('adult', $pet->life_stage);
        $this->assertSame(2, $stageUps); // baby → juvenile → adult
        $this->assertGreaterThanOrEqual(6, (int) $pet->level);

        // Bertelur → generasi 2 (masih Perunggu) + masuk Hall of Fame
        $this->actingAs($user->fresh())->postJson('/employee/pet/breed')
            ->assertJsonPath('success', true)
            ->assertJsonPath('generation', 2)
            ->assertJsonPath('egg_tier', 'bronze')
            ->assertJsonPath('egg_tier_label', 'Perunggu');

        $pet->refresh();
        $this->assertSame('egg', $pet->life_stage);
        $this->assertSame('egg', $pet->status);
        $this->assertSame(2, (int) $pet->generation);
        $this->assertSame(0, (int) $pet->crack_points);
        $this->assertNull($pet->pet_id);
        $this->assertSame(1, $user->petArchive()->count());
        $this->assertSame('Kwek', $user->petArchive()->first()->nickname);

        // Siklus berulang: telur generasi 2 bisa langsung dipecahkan lagi
        $this->actingAs($user->fresh())->postJson('/employee/pet/crack')
            ->assertJsonPath('success', true);
    }

    public function test_egg_tier_upgrades_with_generation(): void
    {
        // Peta tier per generasi: Perunggu → Perak (Gen 3) → Emas (Gen 5) → Kristal (Gen 8)
        $this->assertSame('bronze', PetService::tierForGeneration(1));
        $this->assertSame('bronze', PetService::tierForGeneration(2));
        $this->assertSame('silver', PetService::tierForGeneration(3));
        $this->assertSame('gold', PetService::tierForGeneration(5));
        $this->assertSame('crystal', PetService::tierForGeneration(8));
        $this->assertSame('crystal', PetService::tierForGeneration(99));

        // Bertelur di Gen 4 → telur baru Gen 5 ber-tier Emas
        $user = $this->employee();
        $this->unlockEmployee($user);
        $this->actingAs($user)->getJson('/employee/pet/status')->assertOk();

        $pet = UserPet::where('user_id', $user->id)->first();
        $pet->update([
            'pet_id' => Pet::first()->id,
            'nickname' => 'Sang Induk',
            'status' => 'hatched',
            'life_stage' => 'adult',
            'generation' => 4,
            'egg_tier' => 'silver',
            'level' => 6,
            'hatched_at' => now()->subDays(30),
        ]);

        $this->actingAs($user->fresh())->postJson('/employee/pet/breed')
            ->assertJsonPath('success', true)
            ->assertJsonPath('generation', 5)
            ->assertJsonPath('egg_tier', 'gold')
            ->assertJsonPath('egg_tier_label', 'Emas')
            ->assertJsonPath('egg_tier_icon', '🥇');

        $pet->refresh();
        $this->assertSame('gold', $pet->egg_tier);

        // Status JSON membawa tier untuk warna telur 3D
        $this->actingAs($user->fresh())->getJson('/employee/pet/status')
            ->assertJsonPath('egg_tier', 'gold')
            ->assertJsonPath('egg_tier_label', 'Emas')
            ->assertJsonPath('next_egg_tier', 'gold'); // Gen 6 tetap Emas
    }

    public function test_feed_fails_without_food_stock(): void
    {
        $user = $this->employee();
        $this->unlockEmployee($user);

        // Pecahkan telur tanpa makanan di inventaris
        $user->update(['points' => 500]);
        $palu = ShopItem::where('slug', 'palu-kayu')->first();
        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($user->fresh())->postJson('/employee/shop/buy', ['item_id' => $palu->id])
                ->assertJsonPath('success', true);
        }
        for ($i = 0; $i < 20; $i++) {
            $this->actingAs($user->fresh())->postJson('/employee/pet/use-tool', ['item_id' => $palu->id]);
        }
        $this->actingAs($user->fresh())->postJson('/employee/pet/hatch')->assertJsonPath('success', true);

        // Tanpa stok makanan → gagal dengan pesan mengarah ke toko
        $this->actingAs($user->fresh())->postJson('/employee/pet/feed')
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', fn ($msg) => str_contains($msg, 'Toko'));
    }

    /* ====================================================================
     * POIN KUIS (ANTI-FARM) & MISI HARIAN
     * ==================================================================== */

    public function test_quiz_points_awarded_only_on_first_completed_attempt(): void
    {
        $user = $this->employee();
        $hr = User::factory()->create(['role' => 'hr']);
        $category = QuizCategory::create(['name' => 'Umum']);

        // Mulai dari nol — tanpa welcome pack
        $this->assertSame(0, (int) $user->fresh()->points);

        $quiz = Quiz::create([
            'title' => 'Kuis Anti-Farm',
            'category_id' => $category->id,
            'created_by' => $hr->id,
            'duration' => 10,
            'status' => 'active',
            'enroll_key' => 'RAHASIA',
        ]);

        foreach ([['apple', 1], ['banana', 2]] as [$answer, $order]) {
            Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => "Apa jawaban {$order}?",
                'question_type' => 'short_answer',
                'correct_answer' => $answer,
                'points' => 5,
                'order_number' => $order,
            ]);
        }

        // Attempt pertama → skor 100 → 20 dasar + 15 bonus = 35 poin
        $this->actingAs($user->fresh());
        $this->post("/employee/quizzes/{$quiz->id}/start")->assertRedirect();
        $attempt1 = UserQuizAttempt::where('user_id', $user->id)->latest('id')->first();
        $this->post("/employee/quizzes/take/{$attempt1->id}/save", ['question_id' => $quiz->questions[0]->id, 'answer' => 'apple']);
        $this->post("/employee/quizzes/take/{$attempt1->id}/save", ['question_id' => $quiz->questions[1]->id, 'answer' => 'banana']);
        $this->post("/employee/quizzes/take/{$attempt1->id}/submit")
            ->assertRedirectContains('result')
            ->assertSessionHas('quiz_points', 35);
        $this->assertSame(35, (int) $user->fresh()->points);

        // Flush flash session attempt-1 agar bersih sebelum attempt-2
        $this->get('/employee/dashboard')->assertOk();

        // Attempt kedua (ulangan) → TIDAK dapat poin lagi
        $this->post("/employee/quizzes/{$quiz->id}/start")->assertRedirect();
        $attempt2 = UserQuizAttempt::where('user_id', $user->id)->latest('id')->first();
        $this->post("/employee/quizzes/take/{$attempt2->id}/save", ['question_id' => $quiz->questions[0]->id, 'answer' => 'apple']);
        $this->post("/employee/quizzes/take/{$attempt2->id}/save", ['question_id' => $quiz->questions[1]->id, 'answer' => 'banana']);
        $this->post("/employee/quizzes/take/{$attempt2->id}/submit")
            ->assertSessionMissing('quiz_points');
        $this->assertSame(35, (int) $user->fresh()->points);
    }

    public function test_daily_quest_claim_flow(): void
    {
        $user = $this->employee();
        $this->actingAs($user);

        // Belum ada kuis hari ini → gagal
        $this->postJson('/employee/quests/claim', ['quest_key' => 'kuis_hari_ini'])
            ->assertJsonPath('success', false);

        // Selesaikan kuis hari ini
        $hr = User::factory()->create(['role' => 'hr']);
        $category = QuizCategory::create(['name' => 'Harian']);
        $quiz = Quiz::create([
            'title' => 'Kuis Misi Harian',
            'category_id' => $category->id,
            'created_by' => $hr->id,
            'duration' => 10,
            'status' => 'active',
        ]);
        UserQuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'started_at' => now(),
            'completed_at' => now(),
            'score' => 80,
            'total_correct' => 1,
            'total_wrong' => 0,
            'status' => 'completed',
        ]);

        $this->postJson('/employee/quests/claim', ['quest_key' => 'kuis_hari_ini'])
            ->assertJsonPath('success', true)
            ->assertJsonPath('reward', 25)
            ->assertJsonPath('balance', 25);

        // Dua kali klaim → ditolak
        $this->postJson('/employee/quests/claim', ['quest_key' => 'kuis_hari_ini'])
            ->assertJsonPath('success', false);

        // Key quest tidak valid → ditolak
        $this->postJson('/employee/quests/claim', ['quest_key' => 'hantu'])
            ->assertStatus(422);
    }
}
