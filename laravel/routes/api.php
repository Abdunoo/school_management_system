<?php

use App\Http\Controllers\{
    UserController,
    TeacherController,
    StudentController,
    SubjectController,
    ClassModelController,
    ClassScheduleController
};
use Illuminate\Support\Facades\Route;

Route::apiResource('/users', UserController::class);
Route::apiResource('/teachers', TeacherController::class);
Route::apiResource('/students', StudentController::class);
Route::apiResource('/subjects', SubjectController::class);
Route::apiResource('/classes', ClassModelController::class);
Route::apiResource('/class-schedules', ClassScheduleController::class);
Route::get('/class-schedules/by-class/{className}', [ClassScheduleController::class, 'getScheduleByClassName']);
