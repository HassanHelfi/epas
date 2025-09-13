<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SnapshotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'performance_score' => $this->performance_score,
            'breakdown' => $this->breakdown,
            'department_rank' => $this->department_rank,
            'snapshot_date' => $this->snapshot_date,
        ];
    }
}
