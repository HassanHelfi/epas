<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceSnapshot extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'performance_score',
        'breakdown',
        'snapshot_date',
    ];

    protected $casts = [
        'breakdown' => 'array',
        'snapshot_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

