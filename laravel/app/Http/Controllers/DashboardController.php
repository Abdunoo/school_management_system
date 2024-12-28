<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Student;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getCounts()
    {
        $counts = [
            'students' => Student::count(),
            'classes' => Classes::count(),
            'teachers' => Teacher::count(),
            'subjects' => Subject::count(),
            'users' => User::count(),
        ];

        return response()->json([
            'status' => 200,
            'message' => 'Counts retrieved successfully',
            'data' => $counts,
        ]);
    }
}
