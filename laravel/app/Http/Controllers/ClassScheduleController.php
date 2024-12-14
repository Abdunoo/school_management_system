<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use Illuminate\Http\Request;

class ClassScheduleController extends Controller
{
    public function index()
    {
        $schedules = ClassSchedule::with(['class', 'subject', 'teacher'])->get();
        return response()->json($schedules);
    }

    public function store(Request $request)
    {
        $schedule = ClassSchedule::create($request->all());
        return response()->json($schedule, 201);
    }

    public function show($id)
    {
        $schedule = ClassSchedule::with(['class', 'subject', 'teacher'])->findOrFail($id);
        return response()->json($schedule);
    }

    public function update(Request $request, $id)
    {
        $schedule = ClassSchedule::findOrFail($id);
        $schedule->update($request->all());
        return response()->json($schedule);
    }

    public function destroy($id)
    {
        ClassSchedule::destroy($id);
        return response()->json(null, 204);
    }
}
