<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassScheduleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Classes::with(['schedules.subject', 'schedules.teacher.user']);

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('schedules', function ($q) use ($search) {
                        $q->where('day', 'like', "%{$search}%")
                            ->orWhereHas('subject', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            })->orWhereHas('teacher.user', function ($q) use ($search) {
                                $q->where('username', 'like', "%{$search}%");
                            })->orWhere('lesson_hours', 'like', "%{$search}%")
                            ->orWhere('duration', 'like', "%{$search}%");
                    });
            }

            if ($request->filled('sortField') && $request->filled('sortOrder')) {
                $sortField = $request->input('sortField');
                $sortOrder = $request->input('sortOrder');

                if (in_array($sortField, ['name', 'academic_year'])) {
                    $query->orderBy($sortField, $sortOrder);
                }
            }

            $perPage = $request->input('per_page', 10);
            $classes = $query->paginate($perPage);

            $classes->getCollection()->transform(function ($class) {
                $totalSubjects = $class->schedules->groupBy('subject_id')->count();
                $totalDuration = $class->schedules->sum('duration');

                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'academic_year' => $class->academic_year,
                    'homeroom_teacher' => $class->homeroomTeacher->user->username ?? null,
                    'total_subjects' => $totalSubjects,
                    'total_duration' => $totalDuration,
                ];
            });

            return $this->json(200, 'Class schedules retrieved successfully', $classes);
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
            'day' => 'required|string',
            'lesson_hours' => 'required|integer',
            'duration' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        // Check for scheduling conflicts
        $conflict = ClassSchedule::where('teacher_id', $request->teacher_id)
            ->where('day', $request->day)
            ->where('lesson_hours', $request->lesson_hours)
            ->exists();

        if ($conflict) {
            return $this->json(422, 'Scheduling conflict: The teacher is already assigned to another class at the same lesson hour on the same day.', null);
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

    public function update(Request $request, $className)
    {
        $validator = Validator::make($request->all(), [
            'schedules' => 'required|array',
            'schedules.*.id' => 'required|exists:class_schedules,id',
            'schedules.*.class_id' => 'required|exists:classes,id',
            'schedules.*.subject_id' => 'required|exists:subjects,id',
            'schedules.*.teacher_id' => 'required|exists:teachers,id',
            'schedules.*.day' => 'required|string',
            'schedules.*.lesson_hours' => 'required|integer',
            'schedules.*.duration' => 'required|integer',
            'schedules.*.time' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        try {
            foreach ($request->input('schedules') as $scheduleData) {
                $schedule = ClassSchedule::findOrFail($scheduleData['id']);
                $schedule->update($scheduleData);
            }
            return $this->json(200, 'Class schedules updated successfully');
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to update class schedules', null, ['error' => $e->getMessage()]);
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

    public function getScheduleByClassName($className)
    {
        try {
            $schedules = ClassSchedule::with(['class', 'subject', 'teacher.user'])
                ->whereHas('class', function ($q) use ($className) {
                    $q->where('name', $className);
                })
                ->get();

            $totalSubjects = $schedules->groupBy('subject_id')->count();
            $totalDuration = $schedules->sum('duration');

            $sortedSchedules = $schedules->sortBy(function ($schedule) {
                return [$schedule->day, $schedule->lesson_hours];
            });

            $response = [
                'schedules' => $sortedSchedules->values()->all(),
                'total_subjects' => $totalSubjects,
                'total_duration' => $totalDuration,
                'list_lesson_hours' => $schedules->pluck('lesson_hours')->unique()->sort()->values()->all(),
            ];

            return $this->json(200, 'Class schedules retrieved successfully', $response);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve class schedules', null, ['error' => $e->getMessage()]);
        }
    }
}
