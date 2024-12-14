<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassTransferLog extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'from_class_id', 'to_class_id', 'transfer_date', 'reason'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function fromClass()
    {
        return $this->belongsTo(Classes::class, 'from_class_id');
    }

    public function toClass()
    {
        return $this->belongsTo(Classes::class, 'to_class_id');
    }
}
