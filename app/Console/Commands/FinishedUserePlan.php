<?php

namespace App\Console\Commands;

use App\Jobs\FinishedUserPlanJob;
use App\Models\UserPlan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandAlias;

class FinishedUserePlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:finished-plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finished plan user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $plan_users = UserPlan::where('status', 'ACTIVE')
                ->where('finished_date', '<=', Carbon::tomorrow()->setTimezone(config('app.timezone'))->toIso8601String())
                ->where('finished_date', '>=', Carbon::today()->setTimezone(config('app.timezone'))->toIso8601String())
                ->get();

            $plan_users->each(function (UserPlan $user_plan) {
                FinishedUserPlanJob::dispatch($user_plan);
            });
            return CommandAlias::SUCCESS;
        } catch (\Exception $e) {
            Log::error(' command:finished plan error : ' . $e->getMessage());
            return CommandAlias::INVALID;
        }
    }
}
