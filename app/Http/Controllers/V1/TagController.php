<?php

namespace App\Http\Controllers\V1;

use App\Models\Tag;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreTagRequest;
use App\Http\Requests\V1\UpdateTagRequest;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            
            // $tags = Tag::paginate(10); 
            $tags = Tag::all(); 
    
            return response()->json([
                'success' => true,
                'message' => 'Tags retrieved successfully',
                'data' => $tags,
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tags',
                'errors' => [
                    'message' => $e->getMessage(),
                ]
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
    public function store(StoreTagRequest $request)
    {
        // $request = $request->validated();
        // return response()->json($request);
        try {
            $tag = Tag::create($request->validated());
    
            return response()->json([
                'success' => true,
                'message' => 'Tag created successfully',
                'data'    => $tag,
            ], 201); 
    
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tag',
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
            // Récupérer le tag par son id
            $tag = Tag::findOrFail($id);
    
            // Réponse en cas de succès
            return response()->json([
                'success' => true,
                'message' => 'Tag retrieved successfully',
                'data'    => $tag,
            ], 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, $id)
    {
        try {
            $tag = Tag::findOrFail($id);
            
            $tag->update($request->validated());
    
            return response()->json([
                'success' => true,
                'message' => 'Tag updated successfully',
                'data'    => $tag,
            ], 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tag',
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
            $tag = Tag::findOrFail($id);
    
            $tag->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Tag deleted successfully',
            ], 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tag',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}
