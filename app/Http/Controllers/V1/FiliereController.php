<?php

namespace App\Http\Controllers\V1;

use App\Models\Filiere;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreFiliereRequest;
use App\Http\Requests\V1\UpdateFiliereRequest;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $filieres = Filiere::all();

            return response()->json([
                'success' => true,
                'message' => 'Filieres retrieved successfully',
                'data'    => $filieres,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve filieres',
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
    public function store(StoreFiliereRequest $request)
    {
        try {
            $filiere = Filiere::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Filiere created successfully',
                'data'    => $filiere,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create filiere',
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
            $filiere = Filiere::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Filiere retrieved successfully',
                'data'    => $filiere,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Filiere not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve filiere',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Filiere $filiere)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFiliereRequest $request, $id)
    {
        try {
            $filiere = Filiere::findOrFail($id);

            $filiere->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Filiere updated successfully',
                'data'    => $filiere,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Filiere not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update filiere',
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
            $filiere = Filiere::findOrFail($id);

            $filiere->delete();

            return response()->json([
                'success' => true,
                'message' => 'Filiere deleted successfully',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Filiere not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete filiere',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}
