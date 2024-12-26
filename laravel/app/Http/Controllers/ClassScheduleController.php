<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassScheduleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ClassSchedule::with(['class', 'subject', 'teacher.user']);

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->whereHas('class', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhere('day', 'like', "%{$search}%")
                  ->orWhereHas('subject', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('teacher.user', function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%");
                })->orWhere('lesson_hours', 'like', "%{$search}%")
                  ->orWhere('duration', 'like', "%{$search}%");
            }

            $perPage = $request->input('per_page', 10);
            $schedules = $query->paginate($perPage);
            return $this->json(200, 'Class schedules retrieved successfully', $schedules);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve class schedules', null, ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        try {
            $schedule = ClassSchedule::create($request->all());
            return $this->json(201, 'Class schedule created successfully', $schedule);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to create class schedule', null, ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $schedule = ClassSchedule::with(['class', 'subject', 'teacher'])->findOrFail($id);
            return $this->json(200, 'Class schedule retrieved successfully', $schedule);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve class schedule', null, ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'exists:classes,id',
            'subject_id' => 'exists:subjects,id',
            'teacher_id' => 'exists:teachers,id',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        try {
            $schedule = ClassSchedule::findOrFail($id);
            $schedule->update($request->all());
            return $this->json(200, 'Class schedule updated successfully', $schedule);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to update class schedule', null, ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = ClassSchedule::findOrFail($id);
            $schedule->delete();
            return $this->json(200, 'Class schedule deleted successfully', null);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to delete class schedule', null, ['error' => $e->getMessage()]);
        }
    }
}
