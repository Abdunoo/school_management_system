<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            // i want to get list number 1 - $totalDuration on most many duration on one day
            $totalDuration = $sortedSchedules->groupBy('day')->map(function ($schedules) {
                return $schedules->sum('duration');
            })->max();


            $response = [
                'dataClass' => Classes::where('name', $className)->first(),
                'schedules' => $sortedSchedules->values()->all(),
                'total_subjects' => $totalSubjects,
                'total_duration' => $totalDuration,
                'list_lesson_hours' => $totalDuration > 0 ? range(1, $totalDuration) : [],
            ];

            return $this->json(200, 'Class schedules retrieved successfully', $response);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve class schedules', null, ['error' => $e->getMessage()]);
        }
    }

    public function bulkUpdate(Request $request)
    {
        $schedules = $request->input('schedules');

        // Validate the request
        $validator = Validator::make($request->all(), [
            'schedules' => 'required|array',
            'schedules.*.id' => 'required|exists:class_schedules,id',
            'schedules.*.class_id' => 'required|exists:classes,id',
            'schedules.*.subject_id' => 'required|exists:subjects,id',
            'schedules.*.teacher_id' => 'required|exists:teachers,id',
            'schedules.*.day' => 'required|string',
            'schedules.*.lesson_hours' => 'required|integer',
            'schedules.*.duration' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        foreach ($schedules as $scheduleData) {
            try {
                // Update the schedule
                $schedule = ClassSchedule::findOrFail($scheduleData['id']);
                $schedule->update($scheduleData);
            } catch (\Exception $e) {
                return $this->json(500, 'Failed to update class schedule', null, ['error' => $e->getMessage()]);
            }
        }

        return $this->json(200, 'Class schedules updated successfully', null);
    }

    public function getSchedulesConflicts()
    {
        // Step 1: Define the CTE
        $cte = DB::table('class_schedules as cs1')
            ->join('class_schedules as cs2', function ($join) {
                $join->on('cs1.teacher_id', '=', 'cs2.teacher_id')
                    ->on('cs1.day', '=', 'cs2.day')
                    ->on('cs1.class_id', '!=', 'cs2.class_id');
            })
            ->join('teachers as t', 'cs1.teacher_id', '=', 't.id')
            ->join('users as u', 't.user_id', '=', 'u.id')
            ->join('classes as c1', 'cs1.class_id', '=', 'c1.id')
            ->join('classes as c2', 'cs2.class_id', '=', 'c2.id')
            ->join('subjects as s1', 'cs1.subject_id', '=', 's1.id')
            ->join('subjects as s2', 'cs2.subject_id', '=', 's2.id')
            ->where(function ($query) {
                $query->whereBetween('cs1.lesson_hours', [
                    DB::raw('cs2.lesson_hours'),
                    DB::raw('cs2.lesson_hours + cs2.duration - 1')
                ])
                    ->orWhereBetween('cs2.lesson_hours', [
                        DB::raw('cs1.lesson_hours'),
                        DB::raw('cs1.lesson_hours + cs1.duration - 1')
                    ]);
            })
            ->select(
                'u.username as teacher_name',
                'c1.name as class_1',
                'c2.name as class_2',
                's1.name as subject_1',
                's2.name as subject_2',
                'cs1.day',
                'cs1.lesson_hours as lesson_hours_1',
                'cs2.lesson_hours as lesson_hours_2'
            );

        // Step 2: Use the CTE in the main query
        $conflicts = DB::table(DB::raw("({$cte->toSql()}) as cte"))
            ->mergeBindings($cte) // Bind the CTE parameters
            ->select(
                'teacher_name',
                DB::raw('ANY_VALUE(class_1) as class_1'), // Use ANY_VALUE for class_1
                DB::raw('ANY_VALUE(class_2) as class_2'), // Use ANY_VALUE for class_2
                DB::raw('ANY_VALUE(subject_1) as subject_1'), // Use ANY_VALUE for subject_1
                DB::raw('ANY_VALUE(subject_2) as subject_2'), // Use ANY_VALUE for subject_2
                DB::raw('ANY_VALUE(day) as day'), // Use ANY_VALUE for day
                DB::raw('MIN(lesson_hours_1) as lesson_hours_1'),
                DB::raw('MIN(lesson_hours_2) as lesson_hours_2')
            )
            ->groupBy('teacher_name') // Group only by teacher_name
            ->orderBy('teacher_name')
            ->orderBy('day')
            ->orderBy('lesson_hours_1')
            ->get();

        // Return the conflicts as a JSON response
        return $this->json(
            200,
            'Conflicting schedules retrieved successfully',
            $conflicts
        );
    }
}
