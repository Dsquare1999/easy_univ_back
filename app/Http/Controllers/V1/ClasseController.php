<?php

namespace App\Http\Controllers\V1;

use App\Models\Classe;
use App\Models\Filiere;
use App\Models\Cycle;
use App\Models\Student;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreClasseRequest;
use App\Http\Requests\V1\UpdateClasseRequest;

use Illuminate\Support\Facades\Auth;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $user = Auth()->user();


            $filieres = Filiere::all();
            $cycles = Cycle::all();
            $classes = Classe::with(['cycle', 'filiere'])->get();
            $my_classes = Student::where('user', $user->id)->get();


            return response()->json([
                'success' => true,
                'message' => 'Classes retrieved successfully',
                'data'    => $classes,
                'filieres' => $filieres,
                'cycles' => $cycles,
                'my_classes' => $my_classes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve classes',
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
    public function store(StoreClasseRequest $request)
    {
        try {
            $classe = Classe::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Classe created successfully',
                'data'    => $classe,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create classe',
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
            $classe = classe::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Classe retrieved successfully',
                'data'    => $classe,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Classe not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve classe',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classe $classe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClasseRequest $request, $id)
    {
        try {
            $classe = Classe::findOrFail($id);

            $classe->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Classe updated successfully',
                'data'    => $classe,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Classe not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update classe',
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
            $classe = Classe::findOrFail($id);

            $classe->delete();

            return response()->json([
                'success' => true,
                'message' => 'Classe deleted successfully',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Classe not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete classe',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}
