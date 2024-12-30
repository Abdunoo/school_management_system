<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Teacher::with('user', 'subjects');

            if ($request->filled('search')) {
                $search = $request->input('search');

                $query->where('nip', 'like', '%' . $search . '%')
                    ->orWhere('telepon', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('username', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('subjects', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            }

            if ($request->filled('sortField') && $request->filled('sortOrder')) {
                $sortField = $request->input('sortField');
                $sortOrder = $request->input('sortOrder');

                if (in_array($sortField, ['nip', 'telepon', 'username', 'email'])) {
                    if ($sortField == 'username' || $sortField == 'email') {
                        $query->join('users', 'teachers.user_id', '=', 'users.id')
                              ->orderBy("users.$sortField", $sortOrder);
                    } else {
                        $query->orderBy($sortField, $sortOrder);
                    }
                }
            }

            $perPage = $request->input('per_page', 10);
            $teachers = $query->paginate($perPage);

            return $this->json(200, 'Teachers retrieved successfully', $teachers);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve teachers', null, ['message' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'required|string|max:255',
            'telepon' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['message' => $validator->errors()]);
        }

        try {
            $teacher = Teacher::create($request->only(['nip', 'telepon', 'user_id']));
            $teacher->subjects()->sync($request->subject_ids);
            return $this->json(201, 'Teacher created successfully', $teacher);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to create teacher', null, ['message' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $teacher = Teacher::with('user', 'subjects')->findOrFail($id);
            return $this->json(200, 'Teacher retrieved successfully', $teacher);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve teacher', null, ['message' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'string|max:255',
            'telepon' => 'string|max:255',
            'user_id' => 'exists:users,id',
            'subject_ids' => 'array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['message' => $validator->errors()]);
        }

        try {
            $teacher = Teacher::with('user', 'subjects')->findOrFail($id);
            $teacher->update($request->only(['nip', 'telepon', 'user_id']));
            if ($request->has('subject_ids')) {
                $teacher->subjects()->sync($request->subject_ids);
            }
            return $this->json(200, 'Teacher updated successfully', $teacher);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to update teacher', null, ['message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $teacher = Teacher::findOrFail($id);
            $teacher->subjects()->detach();
            $teacher->delete();
            return $this->json(200, 'Teacher deleted successfully', null);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to delete teacher', null, ['message' => $e->getMessage()]);
        }
    }
}
