<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display a paginated list of students.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Student::with('user');

            // Search by NIS, username, or email
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where('nis', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('username', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                    });
            }

            // Sorting
            if ($request->filled('sortField') && $request->filled('sortOrder')) {
                $sortField = $request->input('sortField');
                $sortOrder = $request->input('sortOrder');

                if (in_array($sortField, ['nis', 'tanggal_lahir', 'alamat', 'gender', 'username', 'email'])) {
                    if ($sortField == 'username' || $sortField == 'email') {
                        $query->join('users', 'students.user_id', '=', 'users.id')
                              ->orderBy("users.$sortField", $sortOrder);
                    } else {
                        $query->orderBy($sortField, $sortOrder);
                    }
                }
            }

            // Pagination
            $perPage = $request->input('per_page', 10);
            $students = $query->paginate($perPage);

            return $this->json(200, 'Students retrieved successfully', $students);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve students', null, ['message' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created student.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|unique:students,nis',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'gender' => 'required|in:male,female',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['message' => $validator->errors()]);
        }

        try {
            $student = Student::create($request->only(['nis', 'tanggal_lahir', 'alamat', 'gender', 'user_id']));
            return $this->json(201, 'Student created successfully', $student);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to create student', null, ['message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified student.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $student = Student::with('user')->findOrFail($id);
            return $this->json(200, 'Student retrieved successfully', $student);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve student', null, ['message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified student.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'sometimes|string|unique:students,nis,' . $id,
            'tanggal_lahir' => 'sometimes|date',
            'alamat' => 'sometimes|string',
            'gender' => 'sometimes|in:male,female',
            'user_id' => 'sometimes|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['message' => $validator->errors()]);
        }

        try {
            $student = Student::findOrFail($id);
            $student->update($request->only(['nis', 'tanggal_lahir', 'alamat', 'gender', 'user_id']));
            return $this->json(200, 'Student updated successfully', $student);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to update student', null, ['message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified student.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->delete();
            return $this->json(200, 'Student deleted successfully', null);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to delete student', null, ['message' => $e->getMessage()]);
        }
    }
}
