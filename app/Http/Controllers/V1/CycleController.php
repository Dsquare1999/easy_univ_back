<?php

namespace App\Http\Controllers\V1;

use App\Models\Cycle;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreCycleRequest;
use App\Http\Requests\V1\UpdateCycleRequest;

class CycleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $cycles = Cycle::all();
    
            return response()->json([
                'success' => true,
                'message' => 'Cycles retrieved successfully',
                'data'    => $cycles,
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
    public function store(StoreCycleRequest $request)
    {
        try {
            $cycle = Cycle::create($request->validated());
    
            return response()->json([
                'success' => true,
                'message' => 'Cycle created successfully',
                'data'    => $cycle,
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create cycle',
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
            $cycle = Cycle::findOrFail($id);
    
            return response()->json([
                'success' => true,
                'message' => 'Cycle retrieved successfully',
                'data'    => $cycle,
            ], 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cycle not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cycle',
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

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCycleRequest $request, $id)
    {
        try {
            $cycle = Cycle::findOrFail($id);
            
            $cycle->update($request->validated());
    
            return response()->json([
                'success' => true,
                'message' => 'Cycle updated successfully',
                'data'    => $cycle,
            ], 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cycle not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cycle',
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
            $cycle = Cycle::findOrFail($id);
    
            $cycle->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Cycle deleted successfully',
            ], 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cycle not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cycle',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}
