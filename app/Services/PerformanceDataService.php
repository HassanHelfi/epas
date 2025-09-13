<?php

namespace App\Services;

use App\Models\Department;
use App\Models\PerformanceReview;
use App\Models\PerformanceSnapshot;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class PerformanceDataService
{
    public function getLatestSnapshotForUser(User $user): ?PerformanceSnapshot
    {
        return PerformanceSnapshot::where('user_id', $user->id)->latest('snapshot_date')->first();
    }

    public function getHistoricalReportsForUser(User $user): EloquentCollection
    {
        return PerformanceSnapshot::where('user_id', $user->id)->orderBy('snapshot_date', 'desc')->get();
    }

    public function createPeerReview(array $validatedData): PerformanceReview
    {
        return PerformanceReview::create($validatedData);
    }

    public function getDepartmentSummary(Department $department): array
    {
        return Cache::remember("department_summary_{$department->id}", now()->addHours(6), function () use ($department) {
            $usersInDept = $department->users()->pluck('id');
            if ($usersInDept->isEmpty()) {
                return $this->emptyDepartmentSummary($department);
            }
            $latestSnapshots = $this->getLatestSnapshotsForUsers($usersInDept);

            return [
                'department_id' => $department->id,
                'department_name' => $department->name,
                'average_score' => round($latestSnapshots->avg('performance_score'), 1),
                'total_employees' => $usersInDept->count(),
                'top_performers' => $this->getPerformers($latestSnapshots, 'desc'),
                'improvement_needed' => $this->getPerformers($latestSnapshots, 'asc'),
            ];
        });
    }

    public function getDepartmentRankings(): Collection
    {
        return Cache::remember('department_rankings', now()->addHours(6), function () {
            $rankingsData = PerformanceSnapshot::query()
                ->select([
                    'departments.id as department_id', 'departments.name as department_name',
                    DB::raw('ROUND(AVG(performance_score), 1) as average_score'),
                    DB::raw('COUNT(DISTINCT users.id) as total_employees')
                ])
                ->join('users', 'performance_snapshots.user_id', '=', 'users.id')
                ->join('departments', 'users.department_id', '=', 'departments.id')
                ->whereIn('performance_snapshots.id', function ($query) {
                    $query->select(DB::raw('MAX(id)'))->from('performance_snapshots')->groupBy('user_id');
                })
                ->groupBy('departments.id', 'departments.name')
                ->orderByDesc('average_score')->get();

            return $rankingsData->map(fn($dept, $key) => array_merge($dept->toArray(), ['rank' => $key + 1]));
        });
    }

    private function getLatestSnapshotsForUsers(Collection $userIds): EloquentCollection
    {
        return PerformanceSnapshot::query()
            ->whereIn('user_id', $userIds)
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))->from('performance_snapshots')->groupBy('user_id');
            })
            ->with('user:id,name')->get();
    }

    private function getPerformers(Collection $snapshots, string $direction, int $count = 3): array
    {
        return $snapshots->sortBy('performance_score', SORT_REGULAR, $direction === 'desc')
            ->take($count)
            ->map(fn($s) => ['id' => $s->user_id, 'name' => $s->user->name, 'score' => $s->performance_score])
            ->values()->all();
    }

    private function emptyDepartmentSummary(Department $department): array
    {
        return [
            'department_id' => $department->id, 'department_name' => $department->name,
            'average_score' => 0.0, 'total_employees' => 0,
            'top_performers' => [], 'improvement_needed' => [],
        ];
    }
}
