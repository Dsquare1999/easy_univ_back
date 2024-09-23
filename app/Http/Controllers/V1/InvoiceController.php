<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreInvoiceRequest;
use App\Http\Requests\V1\UpdateInvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $invoices = Invoice::with(['tag', 'operations', 'user'])->get();

            return response()->json([
                'success' => true,
                'message' => 'Invoices retrieved successfully',
                'data'    => $invoices,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoices',
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
    public function store(StoreInvoiceRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $invoice = Invoice::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data'    => $invoice,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice',
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
            $invoice = Invoice::with(['tag', 'operations'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Invoice retrieved successfully',
                'data'    => $invoice,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoice',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    public function getInvoicesByUser($user_id)
    {
        try {
            $invoices = Invoice::with(['tag', 'operations'])->where('user_id', $user_id)->get();

            if ($invoices->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No invoices found for this user',
                    'data'    => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoices retrieved successfully',
                'data'    => $invoices
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoices',
                'errors'  => ['message' => $e->getMessage()]
            ], 500);
        }
    }

    public function getInvoicesByClasse($classe_id)
    {
        try {
            $invoices = Invoice::with(['tag', 'operations'])->where('classe_id', $classe_id)->get();

            if ($invoices->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No invoices found for this classe',
                    'data'    => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoices retrieved successfully',
                'data'    => $invoices
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoices',
                'errors'  => ['message' => $e->getMessage()]
            ], 500);
        }
    }

    public function getInvoicesByUserAndClasse($user_id, $classe_id)
    {
        try {
            $invoices = Invoice::with(['tag', 'operations'])->where('user_id', $user_id)->where('classe_id', $classe_id)->get();

            if ($invoices->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No invoices found for this user and classe',
                    'data'    => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoices retrieved successfully',
                'data'    => $invoices
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoices',
                'errors'  => ['message' => $e->getMessage()]
            ], 500);
        }
    }

    public function getInvoicesByTag($tag_id)
    {
        try {
            $invoices = Invoice::with(['tag', 'operations'])->where('tag_id', $tag_id)->get();

            if ($invoices->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No invoices found for this classe',
                    'data'    => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoices retrieved successfully',
                'data'    => $invoices
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoices',
                'errors'  => ['message' => $e->getMessage()]
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            $invoice->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Invoice updated successfully',
                'data'    => $invoice,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice',
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
            $invoice = Invoice::findOrFail($id);

            $invoice->delete();

            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete invoice',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}