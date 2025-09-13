<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StorePeerReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reviewer_id' => 'required|exists:users,id',
            'reviewee_id' => 'required|exists:users,id|different:reviewer_id',
            'score' => 'required|integer|min:1|max:10',
            'comments' => 'nullable|string|max:2000',
            'review_date' => 'nullable|date|before_or_equal:today',
        ];
    }
}

