<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with('user', 'subject');

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where('nip', 'like', '%' . $search . '%')
            ->orWhere('telepon', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('username', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('subject', function ($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
        }

        $perPage = $request->input('per_page', 10);

        $teachers = $query->paginate($perPage);

        return response()->json($teachers);
    }

    // public function export(Request $request)
    // {
    //     return Excel::download(new TeachersExport($request->search), 'teachers_export.xlsx');
    // }

    public function store(Request $request)
    {
        $teacher = Teacher::create($request->all());
        return response()->json($teacher, 201);
    }

    public function show($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        return response()->json($teacher);
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::with('user', 'subject')->findOrFail($id);
        $teacher->update($request->only(['nip', 'telepon'])); // Only update fields related to Teacher
        if ($request->has('user')) {
            $teacher->user->update($request->input('user'));
        }
        if ($request->has('subject')) {
            $teacher->subject->update($request->input('subject'));
        }
        return response()->json($teacher);
    }

    public function destroy($id)
    {
        Teacher::destroy($id);
        return response()->json(null, 204);
    }
}
