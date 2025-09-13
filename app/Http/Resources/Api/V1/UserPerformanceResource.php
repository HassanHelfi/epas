<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPerformanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'employee_id' => $this->user->id,
            'name' => $this->user->name,
            'department' => $this->user->department?->name ?? 'N/A',
            'performance_score' => $this->performance_score,
            'breakdown' => $this->breakdown,
            'department_rank' => $this->department_rank,
            'total_employees_in_department' => $this->total_in_department,
            'last_calculated' => $this->created_at->toIso8601String(),
        ];
    }
}
