<?php

namespace App\Console\Commands;

use App\Models\FootballMatch;
use App\Models\User;
use App\Notifications\PendingMatchResultsNotification;
use Illuminate\Console\Command;

class CheckPendingMatchResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:check-pending-results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify admins about matches that likely need an official result.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $matches = FootballMatch::query()
            ->with(['tournament', 'homeTeam', 'awayTeam'])
            ->whereIn('status', ['scheduled', 'live'])
            ->whereNull('home_score')
            ->whereNull('away_score')
            ->where('starts_at', '<', now()->subHours(config('polla.pending_result_after_hours')))
            ->get();

        if ($matches->isEmpty()) {
            $this->info('No pending match results.');

            return self::SUCCESS;
        }

        User::role('super_admin')->get()->each(fn (User $admin) => $admin->notify(new PendingMatchResultsNotification($matches->count())));

        $this->info("Pending matches notified: {$matches->count()}");

        return self::SUCCESS;
    }
}
