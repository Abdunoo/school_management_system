<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        // Start building the query with the Teacher model and eager load the 'user' relationship
        $query = Teacher::with('user');

        // Check if the 'search' parameter is present in the request
        if ($request->filled('search')) {
            $query->where('nip', 'like', '%' . $request->search . '%') // Search by 'nip'
                ->orWhere('spesialisasi', 'like', '%' . $request->search . '%'); // Search by 'spesialisasi'
        }

        // Get the 'per_page' value from the request, default to 10 if not provided
        $perPage = $request->input('per_page', 10);

        // Paginate the results with the specified number of items per page
        $teachers = $query->paginate($perPage);

        // Return the paginated data as a JSON response
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
        $teacher = Teacher::with('user')->findOrFail($id);
        $teacher->update($request->only(['nip', 'spesialisasi', 'telepon'])); // Only update fields related to Teacher
        if ($request->has('user')) {
            $teacher->user->update($request->input('user'));
        }
        return response()->json($teacher);
    }

    public function destroy($id)
    {
        Teacher::destroy($id);
        return response()->json(null, 204);
    }
}
