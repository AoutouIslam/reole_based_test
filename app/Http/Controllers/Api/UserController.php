<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash; 


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $query = User::with('role')->orderBy('id', 'desc');


        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
        'message' => 'Users retrieved successfully',
            'data' => $users->toArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
         $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user->load('role'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('role')->findOrFail($id);
        return response()->json(
            [
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => $user
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {

       // removed the user check because the middleware is handeling it*
        $user = User::findOrFail($id);

        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }
        
        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user->load('role'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // removed this part because the middleware is handeling it 
       //   $authUser = auth()->user();

    // Check if the logged-in user is an admin
   // if (!$authUser || $authUser->hasRole('admin') ) {
     //   return response()->json([
       //     'message' => 'Forbidden: Only admin users can delete accounts.'
       // ], 403);
    //}
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted']);
    }
}
