<?php

namespace App\Http\Controllers\V1;

use App\Models\Program;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreProgramRequest;
use App\Http\Requests\V1\ReportProgramRequest;
use App\Http\Requests\V1\UpdateProgramRequest;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $programs = Program::with(['teacher', 'report'])->get();

            return response()->json([
                'success' => true,
                'message' => 'Programs retrieved successfully',
                'data'    => $programs
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve programs',
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
    public function store(StoreProgramRequest $request)
    {
        try {
            $program = Program::create($request->validated());
            $program = Program::with(['teacher', 'report'])->findOrFail($program->id);

            return response()->json([
                'success' => true,
                'message' => 'Program created successfully',
                'data'    => $program,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create program',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Report a newly created resource in storage.
     */
    public function report(ReportProgramRequest $request)
    {
        try {

            $validated_data = $request->validated();
            
            $program = Program::create($request->validated());
            $program = Program::with(['teacher', 'report'])->findOrFail($program->id);

            $reported_observation = $validated_data['reported_observation'];
            $reported_status = $validated_data['reported_status'];

            $reported_program = Program::findOrFail($validated_data['reported_id']);
            $reported_program->update([
                'observation' => $reported_observation,
                'status' => $reported_status,
                'report' => $program->id,
            ]);
            $reported_program = Program::with(['teacher', 'report'])->findOrFail($reported_program->id);



            return response()->json([
                'success' => true,
                'message' => 'Program created successfully',
                'data'    => [$reported_program, $program],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create program',
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
            $program = Program::findOrFail($id);
            $program = Program::with(['teacher', 'report'])->findOrFail($program->id);

            return response()->json([
                'success' => true,
                'message' => 'Program retrieved successfully',
                'data'    => $program,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Program not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve program',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProgramRequest $request, $id)
    {
        try {
            $program = Program::findOrFail($id);

            $program->update($request->validated());
            $program = Program::with(['teacher', 'report'])->findOrFail($program->id);

            return response()->json([
                'success' => true,
                'message' => 'Program updated successfully',
                'data'    => $program,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Program not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update program',
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
            $program = Program::findOrFail($id);

            $program->delete();

            return response()->json([
                'success' => true,
                'message' => 'Program deleted successfully',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Program not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete program',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}
