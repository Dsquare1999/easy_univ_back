<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Http\Requests\V1\StoreArchiveRequest;
use App\Http\Requests\V1\UpdateArchiveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Archive::query();

            // Filtrer par année si fourni
            if ($request->has('year')) {
                $query->where('year', $request->year);
            }

            // Recherche par nom
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            return response()->json([
                'success' => true,
                'message' => 'Archives récupérées avec succès',
                'data' => $query->get(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des archives',
                'errors' => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArchiveRequest $request)
    {
        try {
            $data = $request->validated();

            // Gérer l'upload du fichier
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('archives', $fileName, 'public');
                $data['file'] = $filePath;
            }

            $archive = Archive::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Archive créée avec succès',
                'data' => $archive,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'archive',
                'errors' => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $archive = Archive::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Archive récupérée avec succès',
                'data' => $archive,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Archive non trouvée',
                'errors' => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'archive',
                'errors' => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArchiveRequest $request, string $id)
    {
        try {
            $archive = Archive::findOrFail($id);
            $data = $request->validated();

            // Gérer l'upload du nouveau fichier
            if ($request->hasFile('file')) {
                // Supprimer l'ancien fichier
                if ($archive->file && Storage::disk('public')->exists($archive->file)) {
                    Storage::disk('public')->delete($archive->file);
                }

                // Uploader le nouveau fichier
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('archives', $fileName, 'public');
                $data['file'] = $filePath;
            }

            $archive->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Archive mise à jour avec succès',
                'data' => $archive,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Archive non trouvée',
                'errors' => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'archive',
                'errors' => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $archive = Archive::findOrFail($id);

            // Supprimer le fichier du stockage
            if ($archive->file && Storage::disk('public')->exists($archive->file)) {
                Storage::disk('public')->delete($archive->file);
            }

            $archive->delete();

            return response()->json([
                'success' => true,
                'message' => 'Archive supprimée avec succès',
                'data' => null,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Archive non trouvée',
                'errors' => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'archive',
                'errors' => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Download the archive file.
     */
    public function download(string $id)
    {
        try {
            $archive = Archive::findOrFail($id);

            if (!$archive->file || !Storage::disk('public')->exists($archive->file)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé',
                ], 404);
            }

            $filePath = Storage::disk('public')->path($archive->file);
            $fileName = basename($archive->file);

            return response()->download($filePath, $fileName);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Archive non trouvée',
                'errors' => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement du fichier',
                'errors' => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}
