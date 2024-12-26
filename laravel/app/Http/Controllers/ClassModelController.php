<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassModelController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Classes::with('homeroomTeacher.user');

            if ($request->filled('search')) {
                $search = $request->input('search');

                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('is_active', $search === 'aktif' ? true : ($search === 'tidak aktif' ? false : true))
                    ->orWhere('academic_year', 'like', '%' . $search . '%')
                    ->orWhereHas('homeroomTeacher.user', function ($q) use ($search) {
                        $q->where('username', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%')
                          ->orWhere('is_active', $search === 'aktif' ? true : ($search === 'tidak aktif' ? false : true));
                    });
            }

            if ($request->filled('sortField') && $request->filled('sortOrder')) {
                $sortField = $request->input('sortField');
                $sortOrder = $request->input('sortOrder');

                if (in_array($sortField, ['name', 'academic_year', 'homeroom_teacher_id', 'is_active'])) {
                    if ($sortField == 'homeroom_teacher_id') {
                        $query->join('teachers', 'classes.homeroom_teacher_id', '=', 'teachers.id')
                              ->orderBy("teachers.user_id", $sortOrder);
                    } else {
                        $query->orderBy($sortField, $sortOrder);
                    }
                }
            }

            $perPage = $request->input('per_page', 10);
            $classes = $query->paginate($perPage);

            return $this->json(200, 'Classes retrieved successfully', $classes);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve classes', null, ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:255',
            'homeroom_teacher_id' => 'required|exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        try {
            $class = Classes::create($request->all());
            return $this->json(201, 'Class created successfully', $class);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to create class', null, ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $class = Classes::with('homeroomTeacher')->findOrFail($id);
            return $this->json(200, 'Class retrieved successfully', $class);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve class', null, ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'academic_year' => 'string|max:255',
            'homeroom_teacher_id' => 'exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        try {
            $class = Classes::findOrFail($id);
            $class->update($request->all());
            return $this->json(200, 'Class updated successfully', $class);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to update class', null, ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $class = Classes::findOrFail($id);
            $class->delete();
            return $this->json(200, 'Class deleted successfully', null);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to delete class', null, ['error' => $e->getMessage()]);
        }
    }
}
