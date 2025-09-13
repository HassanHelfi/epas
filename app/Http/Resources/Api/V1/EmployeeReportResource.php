<?php

namespace App\Http\Resources\Api\V1;

use App\Models\PerformanceSnapshot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'employee_id' => $this->id,
            'name' => $this->name,
            'historical_performance' => SnapshotResource::collection(
                $this->whenLoaded('performanceSnapshots', 
                    $this->performanceSnapshots, 
                    fn() => PerformanceSnapshot::where('user_id', $this->id)
                                ->orderByDesc('snapshot_date')
                                ->get()
                )
            ),
        ];
    }
}
