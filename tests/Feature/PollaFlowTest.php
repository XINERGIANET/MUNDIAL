<?php

namespace Tests\Feature;

use App\Models\FootballMatch;
use App\Models\PhoneVerificationCode;
use App\Models\Prediction;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentParticipant;
use App\Models\TournamentPhase;
use App\Models\User;
use App\Services\MatchResultService;
use App\Services\OtpService;
use App\Services\RankingService;
use App\Services\PredictionScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PollaFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_phone_otp_can_be_verified(): void
    {
        $user = User::factory()->create(['phone_verified_at' => null]);

        PhoneVerificationCode::create([
            'user_id' => $user->id,
            'phone' => $user->phone_normalized,
            'code_hash' => Hash::make('123456'),
            'channel' => 'whatsapp',
            'expires_at' => now()->addMinutes(10),
        ]);

        app(OtpService::class)->verify($user, '123456');

        $this->assertNotNull($user->fresh()->phone_verified_at);
    }

    public function test_user_can_request_tournament_registration(): void
    {
        [$user, $tournament] = $this->userAndTournament();

        $this->actingAs($user)->post(route('tournaments.register', $tournament))->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('tournament_participants', [
            'user_id' => $user->id,
            'tournament_id' => $tournament->id,
            'status' => 'pending_payment',
        ]);
    }

    public function test_prediction_is_blocked_when_participant_is_not_approved(): void
    {
        [$user, $tournament, $match] = $this->approvedSetup(approved: false);

        $this->actingAs($user)->post(route('predictions.store', $match), [
            'predicted_home_score' => 1,
            'predicted_away_score' => 0,
        ])->assertForbidden();
    }

    public function test_approved_participant_can_save_prediction(): void
    {
        [$user, $tournament, $match] = $this->approvedSetup();

        $this->actingAs($user)->post(route('predictions.store', $match), [
            'predicted_home_score' => 2,
            'predicted_away_score' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('predictions', [
            'user_id' => $user->id,
            'match_id' => $match->id,
            'predicted_home_score' => 2,
            'predicted_away_score' => 1,
        ]);
    }

    public function test_prediction_is_blocked_after_closing_time(): void
    {
        [$user, $tournament, $match] = $this->approvedSetup();
        $match->update(['prediction_closes_at' => now()->subMinute()]);

        $this->actingAs($user)->post(route('predictions.store', $match), [
            'predicted_home_score' => 1,
            'predicted_away_score' => 0,
        ])->assertForbidden();
    }

    public function test_scoring_exact_correct_and_wrong(): void
    {
        [$user, $tournament, $match] = $this->approvedSetup();
        $match->update(['home_score' => 2, 'away_score' => 2]);
        $service = app(PredictionScoringService::class);

        $exact = Prediction::create(['tournament_id' => $tournament->id, 'match_id' => $match->id, 'user_id' => $user->id, 'predicted_home_score' => 2, 'predicted_away_score' => 2]);
        $this->assertSame(['points' => 5, 'type' => 'exact_score'], $service->score($exact, $match));

        $correct = new Prediction(['predicted_home_score' => 1, 'predicted_away_score' => 1]);
        $this->assertSame(['points' => 3, 'type' => 'correct_result'], $service->score($correct, $match));

        $wrong = new Prediction(['predicted_home_score' => 2, 'predicted_away_score' => 1]);
        $this->assertSame(['points' => 0, 'type' => 'wrong'], $service->score($wrong, $match));
    }

    public function test_ranking_is_recalculated(): void
    {
        [$user, $tournament, $match] = $this->approvedSetup();
        $match->update(['home_score' => 1, 'away_score' => 0, 'status' => 'finished']);
        Prediction::create(['tournament_id' => $tournament->id, 'match_id' => $match->id, 'user_id' => $user->id, 'predicted_home_score' => 1, 'predicted_away_score' => 0]);

        app(RankingService::class)->recalculateMatchPredictions($match->fresh(['tournament']));
        app(RankingService::class)->recalculateTournamentRanking($tournament);

        $this->assertDatabaseHas('tournament_rankings', [
            'user_id' => $user->id,
            'tournament_id' => $tournament->id,
            'total_points' => 5,
            'position' => 1,
        ]);
    }

    public function test_admin_can_register_result_and_update_points(): void
    {
        [$user, $tournament, $match] = $this->approvedSetup();
        $admin = User::factory()->create();

        Prediction::create(['tournament_id' => $tournament->id, 'match_id' => $match->id, 'user_id' => $user->id, 'predicted_home_score' => 3, 'predicted_away_score' => 1]);

        app(MatchResultService::class)->register($match, 3, 1, $admin);

        $this->assertDatabaseHas('predictions', [
            'match_id' => $match->id,
            'user_id' => $user->id,
            'points_awarded' => 5,
            'result_type' => 'exact_score',
        ]);
    }

    private function userAndTournament(): array
    {
        Queue::fake();

        $user = User::factory()->create();
        $tournament = Tournament::create([
            'name' => 'Demo',
            'slug' => 'demo',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'status' => 'open',
            'is_active' => true,
        ]);

        return [$user, $tournament];
    }

    private function approvedSetup(bool $approved = true): array
    {
        [$user, $tournament] = $this->userAndTournament();
        $phase = TournamentPhase::create(['tournament_id' => $tournament->id, 'name' => 'Fase', 'type' => 'group_stage', 'order' => 1]);
        $home = Team::create(['name' => 'Home', 'slug' => 'home']);
        $away = Team::create(['name' => 'Away', 'slug' => 'away']);
        $match = FootballMatch::create([
            'tournament_id' => $tournament->id,
            'phase_id' => $phase->id,
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'starts_at' => now()->addDay(),
            'prediction_closes_at' => now()->addHours(20),
            'status' => 'scheduled',
        ]);

        TournamentParticipant::create([
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
            'status' => $approved ? 'approved' : 'pending_payment',
            'payment_status' => $approved ? 'paid' : 'unpaid',
        ]);

        return [$user, $tournament, $match];
    }
}
