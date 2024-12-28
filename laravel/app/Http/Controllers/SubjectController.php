<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Subject::query();

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('sortField') && $request->filled('sortOrder')) {
                $sortField = $request->input('sortField');
                $sortOrder = $request->input('sortOrder');

                if (in_array($sortField, ['name', 'created_at'])) {
                    $query->orderBy($sortField, $sortOrder);
                }
            }

            $perPage = $request->input('per_page', 10);
            $subjects = $query->paginate($perPage);

            return $this->json(Response::HTTP_OK, 'Subjects retrieved successfully', $subjects);
        } catch (\Exception $e) {
            return $this->json(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to retrieve subjects', null, ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        try {
            $subject = Subject::create($request->all());
            return $this->json(201, 'Subject created successfully', $subject);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to create subject', null, ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            return $this->json(200, 'Subject retrieved successfully', $subject);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve subject', null, ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        try {
            $subject = Subject::findOrFail($id);
            $subject->update($request->only(['name', 'description']));

            if ($request->has('teacher_ids')) {
                $subject->teachers()->sync($request->teacher_ids);
            }

            return $this->json(200, 'Subject updated successfully', $subject);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to update subject', null, ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            $subject->teachers()->detach();
            $subject->delete();
            return $this->json(200, 'Subject deleted successfully', null);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to delete subject', null, ['error' => $e->getMessage()]);
        }
    }
}
