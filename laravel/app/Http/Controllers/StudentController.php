<?php

namespace App\Http\Controllers;

use App\Helpers\ApplicationResponse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Student::with('user');

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $perPage = $request->input('per_page', 10);
            $students = $query->paginate($perPage);
            return $this->json(200, 'Students retrieved successfully', $students);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve students', null, ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classes,id',
            'enrollment_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        try {
            $student = Student::create($request->all());
            return $this->json(201, 'Student created successfully', $student);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to create student', null, ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $student = Student::with('user')->findOrFail($id);
            return $this->json(200, 'Student retrieved successfully', $student);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve student', null, ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'exists:users,id',
            'class_id' => 'exists:classes,id',
            'enrollment_date' => 'date',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        try {
            $student = Student::findOrFail($id);
            $student->update($request->all());
            return $this->json(200, 'Student updated successfully', $student);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to update student', null, ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->delete();
            return $this->json(200, 'Student deleted successfully', null);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to delete student', null, ['error' => $e->getMessage()]);
        }
    }
}
