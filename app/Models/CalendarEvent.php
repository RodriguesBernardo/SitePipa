<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'visibility',
        'color',
        'user_id',
        'participants'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'participants' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getParticipantsUsersAttribute()
    {
        if (!$this->participants) {
            return collect();
        }

        return User::whereIn('id', $this->participants)->get();
    }

    public function isParticipant($userId)
    {
        return in_array($userId, $this->participants ?? []);
    }

    public function canView($userId)
    {
        return $this->visibility === 'public' || 
               $this->user_id === $userId || 
               $this->isParticipant($userId);
    }
}