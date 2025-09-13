<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class PerformanceCalculatorService
{
    public const REQUIRED_USER_RELATIONS = [
        'tasks.project', 'reviewsReceived', 'trainings', 'department'
    ];

    private const WEIGHTS = [
        'tasks' => 0.30, 'deadlines' => 0.25,
        'reviews' => 0.25, 'training' => 0.20,
    ];

    public function calculateForUsers(Collection $users): Collection
    {
        return $users->map(fn(User $user) => $this->calculateSingleUserPerformance($user));
    }

    private function calculateSingleUserPerformance(User $user): array
    {
        $taskCompletionRate = $this->calculateTaskCompletionRate($user->tasks);
        $deadlineAdherence = $this->calculateDeadlineAdherence($user->tasks);
        $peerReviewScore = $this->calculatePeerReviewScore($user->reviewsReceived);
        $trainingCompletion = $this->calculateTrainingCompletion($user->trainings);

        $performanceScore = (
            ($taskCompletionRate * self::WEIGHTS['tasks']) +
            ($deadlineAdherence * self::WEIGHTS['deadlines']) +
            ($peerReviewScore * self::WEIGHTS['reviews']) +
            ($trainingCompletion * self::WEIGHTS['training'])
        );

        if ($this->isNewUser($user)) {
            $performanceScore = max($performanceScore, 50.0);
        }

        return [
            'employee_id' => $user->id,
            'name' => $user->name,
            'department' => $user->department?->name ?? 'N/A',
            'performance_score' => round($performanceScore, 1),
            'breakdown' => [
                'task_completion'   => round($taskCompletionRate, 1),
                'deadline_adherence'=> round($deadlineAdherence, 1),
                'peer_reviews'      => round($peerReviewScore, 1),
                'training_completion'=> round($trainingCompletion, 1),
            ],
        ];
    }

    private function calculateTaskCompletionRate(Collection $tasks): float
    {
        if ($tasks->isEmpty()) return 0.0;
        $completedTasks = $tasks->where('status', 'completed')->whereNotNull('completed_at');
        if ($completedTasks->isEmpty()) return 0.0;

        $onTime = $completedTasks->filter(fn($t) => $t->completed_at->lte($t->due_date))->count();
        $late = $completedTasks->filter(fn($t) => $t->completed_at->gt($t->due_date))->count() * 0.5;

        return min((($onTime + $late) / $tasks->count()) * 100, 100.0);
    }

    private function calculateDeadlineAdherence(Collection $tasks): float
    {
        $relevantTasks = $tasks->filter(fn ($task) => $task->project && $task->project->start_date >= now()->subYear());
        if ($relevantTasks->isEmpty()) return 0.0;

        $onTimeCount = $relevantTasks->where('status', 'completed')
            ->filter(fn ($t) => $t->completed_at && $t->completed_at->lte($t->due_date))
            ->count();

        return ($onTimeCount / $relevantTasks->count()) * 100;
    }

    private function calculatePeerReviewScore(Collection $reviews): float
    {
        if ($reviews->count() < 3) return 0.0;
        return ($reviews->avg('score') / 10) * 100;
    }

    private function calculateTrainingCompletion(Collection $trainings): float
    {
        $requiredTrainings = $trainings->filter(fn ($t) => $t->pivot->is_required && $t->pivot->assigned_date >= now()->startOfYear());
        if ($requiredTrainings->isEmpty()) return 100.0;

        $completed = $requiredTrainings->where('pivot.status', 'completed')->count();
        return ($completed / $requiredTrainings->count()) * 100;
    }

    private function isNewUser(User $user): bool
    {
        return $user->hire_date && $user->hire_date->diffInMonths(now()) < 3;
    }
}
