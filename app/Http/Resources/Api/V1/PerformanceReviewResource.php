<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformanceReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reviewer_id' => $this->reviewer_id,
            'reviewee_id' => $this->reviewee_id,
            'score' => $this->score,
            'comments' => $this->comments,
            'review_date' => $this->review_date,
        ];
    }
}
