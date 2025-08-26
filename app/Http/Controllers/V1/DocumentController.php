<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $classe = request()->query('classe');
            $filiere = request()->query('filiere');
            $cycle = request()->query('cycle');
            $year = request()->query('year');
            $query = Document::query();
            if ($classe) {
                $query->where('classe', $classe);
            }
            if ($filiere) {
                $query->where('filiere', $filiere);
            }
            if ($cycle) {
                $query->where('cycle', $cycle);
            }
            if ($year) {
                $query->where('year', $year);
            }   
            $documents = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Documents retrieved successfully',
                'data'    => $documents,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve documents',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $document = Document::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Document retrieved successfully',
                'data'    => $document,
            ], 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve document',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $document = Document::findOrFail($id);

            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully',
            ], 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}
