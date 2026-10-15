<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Models\ShopItem;
use App\Models\User;
use App\Models\UserPet;
use App\Models\UserQuizAttempt;
use Database\Seeders\PetSeeder;
use Database\Seeders\ShopItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Uji end-to-end loop ekonomi baru:
 * poin (kuis/video) → beli alat → pecahkan telur → bayi → dewasa → bertelur.
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

    public function test_all_employee_pages_render(): void
    {
        $user = $this->employee();

        foreach ([
            '/employee/dashboard',
            '/employee/shop',
            '/employee/pet',
            '/employee/quizzes/join',
            '/employee/video-challenges',
            '/employee/materi',
            '/employee/profile',
        ] as $uri) {
            $this->actingAs($user)->get($uri)->assertOk();
        }
    }

    public function test_welcome_pack_granted_once_with_starter_items(): void
    {
        $user = $this->employee();

        $res = $this->actingAs($user)->getJson('/employee/pet/status')->assertOk();
        $res->assertJsonPath('points', 50)
            ->assertJsonPath('life_stage', 'egg')
            ->assertJsonPath('tools.0.name', 'Palu Kayu')
            ->assertJsonPath('tools.0.quantity', 8)
            ->assertJsonPath('foods.0.name', 'Susu Botol')
            ->assertJsonPath('foods.0.quantity', 2);

        // Klaim kedua tidak mengulang
        $this->actingAs($user->fresh())->getJson('/employee/pet/status')->assertOk();
        $this->assertSame(50, (int) $user->fresh()->points);
    }

    public function test_buy_requires_enough_points_then_succeeds(): void
    {
        $user = $this->employee();
        $turbo = ShopItem::where('slug', 'bor-turbo')->first();

        // Saldo 0 (welcome pack belum diklaim) → gagal
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

    public function test_full_pet_lifecycle_egg_to_breed(): void
    {
        $user = $this->employee();
        $this->actingAs($user);

        // Aktivasi welcome pack
        $this->getJson('/employee/pet/status')->assertOk();

        // Pukul telur 8× palu kayu (5 retak × 8 = 40)
        $palu = ShopItem::where('slug', 'palu-kayu')->first();
        for ($i = 0; $i < 8; $i++) {
            $this->postJson('/employee/pet/use-tool', ['item_id' => $palu->id])
                ->assertJsonPath('success', true);
        }
        $pet = UserPet::where('user_id', $user->id)->first();
        $this->assertSame(40, (int) $pet->crack_points);

        // Ketukan tangan sampai 100%
        $res = null;
        for ($i = 0; $i < 60; $i++) {
            $res = $this->postJson('/employee/pet/crack')->assertOk();
        }
        $res->assertJsonPath('ready_to_hatch', true);
        $this->assertSame(100, (int) $pet->fresh()->crack_points);
        $this->assertSame('ready_to_hatch', $pet->fresh()->status);

        // Menetas → tahap Bayi
        $this->postJson('/employee/pet/hatch', ['nickname' => 'Kwek'])
            ->assertJsonPath('success', true)
            ->assertJsonPath('life_stage', 'baby');
        $pet->refresh();
        $this->assertSame('baby', $pet->life_stage);
        $this->assertNotNull($pet->pet_id);

        // Beli pakan premium lalu rawat hingga Dewasa
        $user->update(['points' => 1000]);
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

        // Bertelur → generasi 2 + arsip
        $this->actingAs($user->fresh())->postJson('/employee/pet/breed')
            ->assertJsonPath('success', true)
            ->assertJsonPath('generation', 2);

        $pet->refresh();
        $this->assertSame('egg', $pet->life_stage);
        $this->assertSame('egg', $pet->status);
        $this->assertSame(2, (int) $pet->generation);
        $this->assertSame(0, (int) $pet->crack_points);
        $this->assertNull($pet->pet_id);
        $this->assertSame(1, $user->petArchive()->count());
        $this->assertSame('Kwek', $user->petArchive()->first()->nickname);
    }

    public function test_feed_fails_without_food_stock(): void
    {
        $user = $this->employee();
        $this->actingAs($user);
        $this->getJson('/employee/pet/status'); // welcome pack

        $palu = ShopItem::where('slug', 'palu-kayu')->first();
        for ($i = 0; $i < 8; $i++) {
            $this->postJson('/employee/pet/use-tool', ['item_id' => $palu->id]);
        }
        for ($i = 0; $i < 60; $i++) {
            $this->postJson('/employee/pet/crack');
        }
        $this->postJson('/employee/pet/hatch')->assertJsonPath('success', true);

        // Habiskan susu welcome pack (2 porsi)
        $this->postJson('/employee/pet/feed')->assertJsonPath('success', true);
        $this->postJson('/employee/pet/feed')->assertJsonPath('success', true);

        // Stok habis → gagal dengan pesan mengarah ke toko
        $res = $this->postJson('/employee/pet/feed')->assertJsonPath('success', false);
        $this->assertStringContainsString('Toko', $res->json('message'));
    }

    public function test_quiz_points_awarded_only_on_first_completed_attempt(): void
    {
        $user = $this->employee();
        $hr = User::factory()->create(['role' => 'hr']);
        $category = QuizCategory::create(['name' => 'Umum']);

        // Klaim welcome pack dulu (50 poin) agar delta poin kuis jelas
        $this->actingAs($user)->getJson('/employee/pet/status');
        $this->assertSame(50, (int) $user->fresh()->points);

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

        // Attempt pertama → skor 100 → 20 dasar + 15 bonus = 35 poin (total 85)
        $this->actingAs($user->fresh());
        $this->post("/employee/quizzes/{$quiz->id}/start")->assertRedirect();
        $attempt1 = UserQuizAttempt::where('user_id', $user->id)->latest('id')->first();
        $this->post("/employee/quizzes/take/{$attempt1->id}/save", ['question_id' => $quiz->questions[0]->id, 'answer' => 'apple']);
        $this->post("/employee/quizzes/take/{$attempt1->id}/save", ['question_id' => $quiz->questions[1]->id, 'answer' => 'banana']);
        $this->post("/employee/quizzes/take/{$attempt1->id}/submit")
            ->assertRedirectContains('result')
            ->assertSessionHas('quiz_points', 35);
        $this->assertSame(85, (int) $user->fresh()->points);

        // Flush flash session attempt-1 agar bersih sebelum attempt-2
        $this->get('/employee/dashboard')->assertOk();

        // Attempt kedua (ulangan) → TIDAK dapat poin lagi
        $this->post("/employee/quizzes/{$quiz->id}/start")->assertRedirect();
        $attempt2 = UserQuizAttempt::where('user_id', $user->id)->latest('id')->first();
        $this->post("/employee/quizzes/take/{$attempt2->id}/save", ['question_id' => $quiz->questions[0]->id, 'answer' => 'apple']);
        $this->post("/employee/quizzes/take/{$attempt2->id}/save", ['question_id' => $quiz->questions[1]->id, 'answer' => 'banana']);
        $this->post("/employee/quizzes/take/{$attempt2->id}/submit")
            ->assertSessionMissing('quiz_points');
        $this->assertSame(85, (int) $user->fresh()->points);
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
