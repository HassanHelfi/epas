<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\PerformanceSnapshot;
use App\Services\PerformanceCalculatorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculatePerformanceSnapshots extends Command
{
    protected $signature = 'performance:calculate';
    protected $description = 'Calculate and store performance snapshots for all users.';

    public function __construct(protected PerformanceCalculatorService $performanceCalculatorService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting performance calculation for all users...');
        Log::info('Performance calculation job started.');

        $users = User::with(PerformanceCalculatorService::REQUIRED_USER_RELATIONS)->get();
        if ($users->isEmpty()) {
            $this->warn('No users found to process.');
            return self::SUCCESS;
        }
        
        $allPerformances = $this->performanceCalculatorService->calculateForUsers($users);

        $departmentGroups = $allPerformances->groupBy('department');
        $rankedPerformances = $departmentGroups->flatMap(function ($performances) {
            $sorted = $performances->sortByDesc('performance_score')->values();
            return $sorted->map(function ($performanceData, $rank) use ($sorted) {
                $performanceData['department_rank'] = $rank + 1;
                $performanceData['total_in_department'] = $sorted->count();
                return $performanceData;
            });
        });

        $this->info('Storing performance snapshots...');
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
        $this->info("\nPerformance calculation completed and snapshots stored successfully.");
        Log::info('Performance calculation job finished.');

        return self::SUCCESS;
    }
}
