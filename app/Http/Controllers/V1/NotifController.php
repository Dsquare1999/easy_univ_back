<?php

namespace App\Http\Controllers\V1;

use App\Models\Notif;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreNotifRequest;
use App\Http\Requests\V1\UpdateNotifRequest;
use Illuminate\Http\Request;

class NotifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $notifs = Notif::all();

            return response()->json([
                'success' => true,
                'message' => 'Notifications retrieved successfully',
                'data'    => $notifs,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notifications',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    public function markAsRead($id)
    {
        $notif = Notif::findOrFail($id);
        $notif->update(['is_read' => true]);
        return response()->json(['message' => 'Notification marquée comme lue']);
    }

    public function markAllAsRead()
    {
        Notif::query()->update(['is_read' => true]);
        return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues']);
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
    public function store(StoreNotifRequest $request)
    {
        try {
            $notif = Notif::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Notification created successfully',
                'data'    => $notif,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Notification',
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
            $notif = Notif::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Notification retrieved successfully',
                'data'    => $notif,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Notification',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notif $notif)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotifRequest $request, $id)
    {
        try {
            $notif = Notif::findOrFail($id);

            $notif->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Notification updated successfully',
                'data'    => $notif,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Notification',
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
            $notif = Notif::findOrFail($id);

            $notif->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Notification',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}
