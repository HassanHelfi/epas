<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'hire_date',
        'position',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'hire_date' => 'date',
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(PerformanceReview::class, 'reviewer_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(PerformanceReview::class, 'reviewee_id');
    }

     public function performanceSnapshots()
    {
        return $this->hasMany(PerformanceSnapshot::class);
    }

    public function projects()
    {
        return $this->hasManyThrough(Project::class, Task::class, 'assigned_to', 'id', 'id', 'project_id');
    }

    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class, 'training_user')
                    ->withPivot('is_required', 'status', 'assigned_date', 'completed_at')
                    ->withTimestamps();
    }
}
