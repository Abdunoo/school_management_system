<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 100); // Default to 100 if not provided
        $search = $request->input('search', '');

        try {
            $query = User::query();

            if ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
            }

            $users = $query->paginate($perPage);
            return $this->json(200, 'Users retrieved successfully', $users);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve users', null, ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        try {
            $user = User::create($request->all());
            return $this->json(201, 'User created successfully', $user);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to create user', null, ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return $this->json(200, 'User retrieved successfully', $user);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to retrieve user', null, ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $id,
            'password' => 'string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->json(422, 'Validation failed', null, ['errors' => $validator->errors()]);
        }

        try {
            $user = User::findOrFail($id);
            $user->update($request->all());
            return $this->json(200, 'User updated successfully', $user);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to update user', null, ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return $this->json(200, 'User deleted successfully', null);
        } catch (\Exception $e) {
            return $this->json(500, 'Failed to delete user', null, ['error' => $e->getMessage()]);
        }
    }
}
