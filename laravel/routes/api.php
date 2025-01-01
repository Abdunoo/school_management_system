<?php

use App\Http\Controllers\{
    UserController,
    TeacherController,
    StudentController,
    SubjectController,
    ClassModelController,
    ClassScheduleController,
    DashboardController
};
use Illuminate\Support\Facades\Route;

Route::apiResource('/users', UserController::class);
Route::apiResource('/teachers', TeacherController::class);
Route::apiResource('/students', StudentController::class);
Route::apiResource('/subjects', SubjectController::class);
Route::apiResource('/classes', ClassModelController::class);
Route::apiResource('/class-schedules', ClassScheduleController::class);
Route::post('/class-schedules/bulk-update', [ClassScheduleController::class, 'bulkUpdate']);
Route::get('/class-schedules/by-class/{className}', [ClassScheduleController::class, 'getScheduleByClassName']);
Route::get('/dashboard/counts', [DashboardController::class, 'getCounts']);
Route::get('/teachers/by-subject-id/{id}', [TeacherController::class,'getTeacherBySubjectId']);


///

Route::get('/class-schedule-conflicts', [ClassScheduleController::class, 'getSchedulesConflicts']);
