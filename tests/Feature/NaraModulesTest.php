<?php

/**
 * Dokumentasi file: Test aplikasi.
 *
 * Menjelaskan tanggung jawab file tests/Feature/NaraModulesTest.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace Tests\Feature;

use App\Models\KnowledgeBaseRule;
use App\Models\MicroActionLog;
use App\Models\RecoveryActivity;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NaraModulesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::where('email', 'nara@wellbeing.id')->first();
    }

    public function test_dashboard_loads_successfully_with_all_signal_vectors()
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('4 Vektor Sinyal Hidup');
        $response->assertSee('Pikiran (Mind)');
        $response->assertSee('Tubuh (Body)');
        $response->assertSee('Sosial (Social)');
        $response->assertSee('Hidup (Life)');
    }

    /** @test */
    public function user_can_submit_daily_checkin()
    {
        $response = $this->actingAs($this->user)->post(route('checkin.store'), [
            'date' => now()->toDateString(),
            'mood_level' => 85,
            'focus_level' => 80,
            'stress_level' => 25,
            'overthinking_level' => 20,
            'sleep_hours' => 8.0,
            'sleep_quality' => 85,
            'energy_level' => 80,
            'physical_activity_min' => 30,
            'social_interaction_score' => 80,
            'loneliness_score' => 15,
            'relationship_friction_score' => 5,
            'workload_score' => 35,
            'financial_pressure_score' => 25,
            'goal_progress_score' => 75,
            'notes' => 'Hari ini segar dan fokus.',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('daily_checkins', [
            'user_id' => $this->user->id,
            'date' => now()->toDateString(),
        ]);
    }

    /** @test */
    public function user_can_toggle_micro_action()
    {
        $microAction = MicroActionLog::firstOrCreate([
            'user_id' => $this->user->id,
            'date' => now()->toDateString(),
            'action_title' => 'Stretching 5 Menit',
            'category' => 'body',
            'is_completed' => false,
        ]);

        $response = $this->actingAs($this->user)->post(route('micro-action.toggle', $microAction->id));

        $response->assertRedirect();
        $this->assertTrue($microAction->fresh()->is_completed);
    }

    /** @test */
    public function pattern_engine_and_what_if_simulator_work()
    {
        $response = $this->actingAs($this->user)->get(route('pattern.index'));

        $response->assertStatus(200);
        $response->assertSee('Life Pattern Engine');
        $response->assertSee('What If');
    }

    /** @test */
    public function recovery_lab_can_log_sessions_and_rank_activities()
    {
        $activity = RecoveryActivity::first();

        $response = $this->actingAs($this->user)->post(route('recovery.sessions.store'), [
            'activity_id' => $activity->id,
            'energy_before' => 40,
            'energy_after' => 75,
            'mood_before' => 45,
            'mood_after' => 80,
            'duration_minutes' => 20,
            'notes' => 'Jalan santai sangat menyegarkan.',
        ]);

        $response->assertRedirect(route('recovery.index'));
        $this->assertDatabaseHas('recovery_sessions', [
            'user_id' => $this->user->id,
            'activity_id' => $activity->id,
        ]);
    }

    /** @test */
    public function pulse_community_trends_accessible()
    {
        $response = $this->actingAs($this->user)->get(route('pulse.index'));

        $response->assertStatus(200);
        $response->assertSee('Pulse: Solidaritas Kesejahteraan Anak Muda');
    }

    /** @test */
    public function support_circle_can_add_member_and_send_safe_ping()
    {
        $response = $this->actingAs($this->user)->post(route('circle.members.store'), [
            'name' => 'Budi Pratama',
            'relationship_type' => 'sahabat',
            'phone' => '08123456789',
            'email' => 'budi@example.com',
        ]);

        $response->assertRedirect(route('circle.index'));
        $this->assertDatabaseHas('support_circle_members', [
            'name' => 'Budi Pratama',
        ]);

        $pingResponse = $this->actingAs($this->user)->post(route('circle.ping'), [
            'support_type' => 'general',
            'custom_short_note' => 'Lagi agak capek aja.',
        ]);

        $pingResponse->assertRedirect(route('circle.index'));
        $this->assertDatabaseHas('support_pings', [
            'user_id' => $this->user->id,
            'support_type' => 'general',
        ]);
    }

    /** @test */
    public function reflection_coach_matches_rule_and_saves_journal()
    {
        $rule = KnowledgeBaseRule::first();

        $response = $this->actingAs($this->user)->post(route('reflection.store'), [
            'rule_id' => $rule->id,
            'prompt_topic' => $rule->title,
            'prompt_snapshot' => $rule->reflection_prompt,
            'question_snapshot' => $rule->guided_question,
            'user_response' => 'Saya merasa lega setelah menuliskan ini.',
            'mood_after' => 80,
        ]);

        $response->assertRedirect(route('reflection.index'));
        $this->assertDatabaseHas('reflection_journals', [
            'user_id' => $this->user->id,
            'user_response' => 'Saya merasa lega setelah menuliskan ini.',
        ]);
    }

    public function test_chat_service_correctly_handles_statements_qa_and_quizzes()
    {
        $chatService = app(ChatService::class);

        // 1. Pernyataan emosi langsung
        $resSedih = $chatService->respond($this->user, 'aku lagi sedih banget');
        $this->assertEquals('feeling_sad', $resSedih['nara_message']->intent_detected);
        $this->assertStringContainsString('Sedih itu boleh', $resSedih['nara_message']->message);

        // 2. Pertanyaan / minta kuis
        $resTanya = $chatService->respond($this->user, 'beri aku pertanyaan');
        $this->assertEquals('reflection_quiz_question', $resTanya['nara_message']->intent_detected);

        // 3. Typo correction tidak salah kaprah
        $resBeri = $chatService->respond($this->user, 'beri aku pertanyaan');
        $this->assertStringNotContainsString('sepi aku pertanyaan', $resBeri['nara_message']->message);

        // 4. Jawaban kuis validasi (Mitos)
        $resMitos = $chatService->respond($this->user, 'Salah (Mitos)');
        $this->assertEquals('quiz_answer_evaluation', $resMitos['nara_message']->intent_detected);
        $this->assertStringContainsString('Tepat sekali', $resMitos['nara_message']->message);

        // 5. Tanya seputar mental health (Depresi)
        $resDepresi = $chatService->respond($this->user, 'apa itu depresi?');
        $this->assertEquals('qa_depression', $resDepresi['nara_message']->intent_detected);

        // 6. Pernyataan burnout / kelelahan
        $resBurnout = $chatService->respond($this->user, 'aku lagi burnout parah');
        $this->assertEquals('burnout', $resBurnout['nara_message']->intent_detected);

        // 7. Variasi penulisan quiz / kuiz
        $resQuiz = $chatService->respond($this->user, 'kasih quiz dong');
        $this->assertEquals('reflection_quiz_question', $resQuiz['nara_message']->intent_detected);

        $resKuiz = $chatService->respond($this->user, 'mau ikutan kuiz mental health');
        $this->assertEquals('reflection_quiz_question', $resKuiz['nara_message']->intent_detected);
    }
}
