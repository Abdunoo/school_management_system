<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nip', 'spesialisasi', 'telepon'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class, 'teacher_id');
    }

    public function class()
    {
        return $this->hasMany(Classes::class, 'homeroom_teacher_id');
    }
}