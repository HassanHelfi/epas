<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Training extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'training_user')
                    ->withPivot('is_required', 'status', 'assigned_date', 'completed_at')
                    ->withTimestamps();
    }
}
