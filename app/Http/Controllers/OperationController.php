<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOperationRequest;
use App\Http\Requests\UpdateOperationRequest;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreOperationRequest $request)
    {
        $operation = Operation::create($request->all());

        return response()->json($operation, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Operation $operation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Operation $operation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOperationRequest $request, Operation $operation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Operation $operation)
    {
        //
    }
}
