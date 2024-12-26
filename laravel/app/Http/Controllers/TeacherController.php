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
            $query = Teacher::with('user', 'subject');

            if ($request->filled('search')) {
                $search = $request->input('search');

                $query->where('nip', 'like', '%' . $search . '%')
                    ->orWhere('telepon', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('username', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%')
                          ->orWhere('is_active', $search === 'aktif' ? true : ($search === 'tidak aktif' ? false : false));
                    })
                    ->orWhereHas('subject', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            }

            $perPage = $request->input('per_page', 10);
            $teachers = $query->paginate($perPage);

            return $this->json(200, 'Teachers retrieved successfully', $teachers);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve teachers', null, ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'required|string|max:255',
            'telepon' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        try {
            $teacher = Teacher::create($request->all());
            return $this->json(201, 'Teacher created successfully', $teacher);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to create teacher', null, ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $teacher = Teacher::with('user', 'subject')->findOrFail($id);
            return $this->json(200, 'Teacher retrieved successfully', $teacher);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve teacher', null, ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'string|max:255',
            'telepon' => 'string|max:255',
            'subject_id' => 'exists:subjects,id',
            'user_id' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        try {
            $teacher = Teacher::with('user', 'subject')->findOrFail($id);
            $teacher->update($request->only(['nip', 'telepon', 'subject_id', 'user_id']));
            return $this->json(200, 'Teacher updated successfully', $teacher);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to update teacher', null, ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $teacher = Teacher::findOrFail($id);
            $teacher->delete();
            return $this->json(200, 'Teacher deleted successfully', null);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to delete teacher', null, ['error' => $e->getMessage()]);
        }
    }
}
