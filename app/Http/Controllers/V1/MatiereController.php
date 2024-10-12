<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Models\Classe;
use App\Models\Matiere;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreMatiereRequest;
use App\Http\Requests\V1\UpdateMatiereRequest;

class MatiereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $matieres = Matiere::with(['classe', 'teacher'])->get();
            $teachers = User::where('type', 1)->get();

            return response()->json([
                'success' => true,
                'message' => 'Matieres retrieved successfully',
                'data'    => $matieres,
                'teachers' => $teachers
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve matieres',
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
    public function store(StoreMatiereRequest $request)
    {
        try {
            $matiere = Matiere::create($request->validated());
            $matiere = Matiere::with(['classe', 'teacher'])->findOrFail($matiere->id);

            return response()->json([
                'success' => true,
                'message' => 'Matiere created successfully',
                'data'    => $matiere,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create matiere',
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
            $matiere = Matiere::findOrFail($id);
            $matiere = Matiere::with(['classe', 'teacher'])->findOrFail($matiere->id);

            return response()->json([
                'success' => true,
                'message' => 'Matiere retrieved successfully',
                'data'    => $matiere,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Matiere not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve matiere',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Matiere $matiere)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMatiereRequest $request, $id)
    {
        try {
            $matiere = Matiere::findOrFail($id);

            $matiere->update($request->validated());
            $matiere = Matiere::with(['classe', 'teacher'])->findOrFail($matiere->id);

            return response()->json([
                'success' => true,
                'message' => 'Matiere updated successfully',
                'data'    => $matiere,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Matiere not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update matiere',
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
            $matiere = Matiere::findOrFail($id);

            $matiere->delete();

            return response()->json([
                'success' => true,
                'message' => 'Matiere deleted successfully',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Matiere not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete matiere',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}
