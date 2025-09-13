<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePeerReviewRequest;
use App\Http\Resources\Api\V1\DepartmentRankingResource;
use App\Http\Resources\Api\V1\DepartmentSummaryResource;
use App\Http\Resources\Api\V1\EmployeeReportResource;
use App\Http\Resources\Api\V1\PerformanceReviewResource;
use App\Http\Resources\Api\V1\UserPerformanceResource;
use App\Jobs\ProcessPerformanceCalculations;
use App\Models\Department;
use App\Models\User;
use App\Services\PerformanceCalculatorService;
use App\Services\PerformanceDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformanceController extends Controller
{
    public function __construct(
        protected readonly PerformanceCalculatorService $performanceService,
        protected readonly PerformanceDataService $performanceDataService
    ) {}

    public function getUserPerformance(User $user): JsonResource|JsonResponse
    {
        $latestSnapshot = $this->performanceDataService->getLatestSnapshotForUser($user);
        if (!$latestSnapshot) {
            return response()->json(['message' => 'Performance data not yet available for this user.'], 404);
        }
        return new UserPerformanceResource($latestSnapshot);
    }

    public function getDepartmentPerformanceSummary(Department $department): JsonResource
    {
        $summary = $this->performanceDataService->getDepartmentSummary($department);
        return new DepartmentSummaryResource($summary);
    }

    public function getDepartmentRankings(): JsonResource
    {
        $rankings = $this->performanceDataService->getDepartmentRankings();
        return DepartmentRankingResource::collection($rankings);
    }

    public function getEmployeePerformanceReport(User $user): JsonResource
    {
        $historicalData = $this->performanceDataService->getHistoricalReportsForUser($user);
        return new EmployeeReportResource($user->setRelation('performanceSnapshots', $historicalData));
    }

    public function triggerPerformanceCalculation(): JsonResponse
    {
        ProcessPerformanceCalculations::dispatch();
        return response()->json(['message' => 'Performance calculation process has been queued and will run in the background.'], 202);
    }

    public function submitPeerReview(StorePeerReviewRequest $request): JsonResponse
    {
        $review = $this->performanceDataService->createPeerReview($request->validated());
        return (new PerformanceReviewResource($review))
            ->additional(['message' => 'Peer review submitted successfully'])
            ->response()
            ->setStatusCode(201);
    }
}

