<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreUserRequest;
use App\Http\Requests\V1\UpdateUserRequest;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::all();
            

            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data'    => $users,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cycles',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        Log::info('Creating user: ', $request->validated());

        try {
            $user = User::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data'    => $user,
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data'    => $user,
            ], 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cycle $cycle)
    {
        //
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            Log::info('Update method called for user: ' . $id);
            
            // Log raw request details
            Log::info('Content-Type: ' . $request->header('Content-Type'));
            Log::info('Request method: ' . $request->method());
            Log::info('Raw request content:', [
                'all' => $request->all(),
                'post' => $_POST,
                'files' => $_FILES,
                'input' => $request->input(),
                'request' => $request->request->all(),
            ]);
            
            $user = User::findOrFail($id);
            
            // Get raw input data
            $input = $request->all();
            Log::info('Raw input data:', $input);
            
            // Process validation
            $validatedData = $request->validated();
            Log::info('Validated data:', $validatedData);
            
            if (empty($validatedData)) {
                Log::warning('No validated data found. Raw input was:', $input);
                return response()->json([
                    'success' => false,
                    'message' => 'No valid data provided for update',
                    'debug_info' => [
                        'raw_input' => $input,
                        'content_type' => $request->header('Content-Type'),
                        'method' => $request->method()
                    ]
                ], 422);
            }
            
            // Update user
            $user->update($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data'    => $user->fresh(),
                'debug_info' => [
                    'raw_input' => $input,
                    'validated_data' => $validatedData,
                    'files_received' => array_keys($request->allFiles())
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ], 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}
