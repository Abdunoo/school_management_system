<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SubjectTeacher extends Pivot
{
    use HasFactory;

    protected $table = 'subject_teacher';

    protected $fillable = ['teacher_id', 'subject_id'];
}
