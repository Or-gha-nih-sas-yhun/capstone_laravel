<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'teacher_first_name',
        'teacher_middle_name',
        'teacher_last_name',
        'teacher_email',
        'contact_number',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    /**
     * Get the user account associated with the teacher.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the groups advised by this teacher.
     */
    public function groups()
    {
        return $this->hasMany(Group::class, 'adviser_id');
    }
    public function evaluations()
{
    return $this->hasMany(Evaluation::class, 'teacher_id');
}
public function sections()
{
    return $this->hasMany(Section::class, 'user_id', 'user_id');
}
public function evaluationRooms()
{
    return $this->belongsToMany(EvaluationRoom::class, 'room_panelists', 'teacher_id', 'room_id')
        ->withTimestamps();
}
// app/Models/Teacher.php
public function hasActiveRoomForStageType(int $stageType, ?int $exceptRoomId = null): bool
{
    return $this->evaluationRooms()
        ->where('evaluation_rooms.is_archived', false)
        ->when($exceptRoomId, fn ($q) => $q->where('evaluation_rooms.id', '!=', $exceptRoomId))
        ->get()
        ->contains(function ($room) use ($stageType) {
            $type = $room->capstoneStage->stage_type
                ?? $room->requiredMilestone?->capstoneStage?->stage_type;
            return (int) $type === $stageType;
        });
}
}