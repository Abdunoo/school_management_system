<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;

class ClassModelController extends Controller
{
    public function index(Request $request)
    {
        $query = Classes::with('homeroomTeacher.user');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('name', 'like', '%' . $search . '%')
                ->orWhereHas('homeroomTeacher.user', function ($q) use ($search) {
                    $q->where('nip', 'like', '%' . $search . '%')
                      ->orWhere('spesialisasi', 'like', '%' . $search . '%');
                })
                ->orWhere('academic_year','like', '%' . $search . '%');
        }

        $perPage = $request->input('per_page', 10);

        $classes = $query->paginate($perPage);

        return response()->json($classes);
    }

    public function store(Request $request)
    {
        $class = Classes::create($request->all());
        return response()->json($class, 201);
    }

    public function show($id)
    {
        $class = Classes::with('homeroomTeacher')->findOrFail($id);
        return response()->json($class);
    }

    public function update(Request $request, $id)
    {
        $class = Classes::findOrFail($id);
        $class->update($request->all());
        return response()->json($class);
    }

    public function destroy($id)
    {
        Classes::destroy($id);
        return response()->json(null, 204);
    }
}
