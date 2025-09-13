<?php

namespace App\Console\Commands;

use App\Models\PerformanceSnapshot;
use App\Models\User;
use App\Services\PerformanceCalculatorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SnapshotPerformanceCommand extends Command
{
    protected $signature = 'performance:snapshot';
    
    protected $description = 'Calculate and store performance snapshots for all active users.';

    /**
     * Create a new command instance.
     */
    public function __construct(protected PerformanceCalculatorService $calculatorService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting performance calculation for all users...');
        Log::info('[Performance Job] Started.');

        $users = User::with(PerformanceCalculatorService::REQUIRED_USER_RELATIONS)->get();
        if ($users->isEmpty()) {
            $this->warn('No users found to process.');
            return self::SUCCESS;
        }

        $allPerformances = $this->calculatorService->calculateForUsers($users);

        $rankedPerformances = $allPerformances->groupBy('department')
            ->flatMap(function ($departmentPerformances) {
                $sorted = $departmentPerformances->sortByDesc('performance_score')->values();
                return $sorted->map(function ($performance, $rank) use ($sorted) {
                    $performance['department_rank'] = $rank + 1;
                    $performance['total_in_department'] = $sorted->count();
                    return $performance;
                });
            });

        $this->info('Storing calculated snapshots in the database...');
        $bar = $this->output->createProgressBar($rankedPerformances->count());
        $bar->start();

        foreach ($rankedPerformances as $data) {
            PerformanceSnapshot::updateOrCreate(
                [
                    'user_id' => $data['employee_id'],
                    'snapshot_date' => now()->toDateString(),
                ],
                [
                    'performance_score' => $data['performance_score'],
                    'breakdown' => $data['breakdown'],
                    'department_rank' => $data['department_rank'],
                    'total_in_department' => $data['total_in_department'],
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n✅ Performance snapshots stored successfully.");
        Log::info('[Performance Job] Finished successfully.');

        return self::SUCCESS;
    }
}

