<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\V1\FichePreInscriptionController;

use App\Models\User;
use App\Models\Student;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Cycle;
use App\Models\Filiere;
use App\Models\Unite;

use Illuminate\Support\Facades\Log;
use App\Models\Releve;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreReleveRequest;
use App\Http\Requests\V1\UpdateReleveRequest;

class ReleveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $releves = Releve::with(['classe.cycle', 'classe.filiere', 'matiere.teacher', 'student.user']);

            if ($matiereId = request()->query('matiere')) {
                $releves->where('matiere', $matiereId);
            }

            $releves = $releves->get();

            return response()->json([
                'success' => true,
                'message' => 'Relevés retrieved successfully',
                'data'    => $releves,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve relevés',
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
    public function store(StoreReleveRequest $request)
    {
        try {
            $releve = Releve::create($request->validated());
            $releve = Releve::with(['matiere', 'student'])->findOrFail($releve->id);

            return response()->json([
                'success' => true,
                'message' => 'Relevé created successfully',
                'data'    => $releve,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create relevé',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    public function mark(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'examType' => 'required|string', 
                'notes' => 'required|array',
                'notes.*.note' => 'nullable|numeric|min:0|max:20', 
                'notes.*.observation' => 'nullable|string'
            ]);
        
            $examType = $validatedData['examType'];
            $notes = $validatedData['notes'];
        
            foreach ($notes as $releveId => $data) {
                $releve = Releve::find($releveId);
                
                if ($releve) {
                    switch ($examType) {
                        case 'exam1':
                            $releve->exam1 = $data['note'] ?? $releve->exam1;
                            $releve->observation_exam1 = $data['observation'] ?? $releve->observation_exam1;
                            break;
                        case 'exam2':
                            $releve->exam2 = $data['note'] ?? $releve->exam2;
                            $releve->observation_exam2 = $data['observation'] ?? $releve->observation_exam2;
                            break;
                        case 'partial':
                            $releve->partial = $data['note'] ?? $releve->partial;
                            $releve->observation_partial = $data['observation'] ?? $releve->observation_partial;
                            break;
                        case 'remedial':
                            $releve->remedial = $data['note' ] ?? $releve->remedial;
                            $releve->observation_remedial = $data['observation'] ?? $releve->observation_remedial;
                            break;
                    }
        
                    $releve->save();
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Les relevés ont été mis à jour avec succès',
                'data'    => [],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Les relevés n\'ont pas pu être mis à jour',
                'errors'  => ['message' => $th->getMessage()],
            ], 500);
        }
    }

    /**
     * Generate PDF for student report.
     */
    public function generate($id)
    {
        try {
            $notes = [];
            $classe = Classe::with(['matieres.unite'])->findOrFail($id);
            $matieres = $classe->matieres;
            Log::info("Matieres: " . $matieres);
            
            $uniteIds = $matieres->pluck('unite')->filter()->unique()->values()->all();

            Log::info("Unite IDs: " . implode(', ', $uniteIds));
            $unites = Unite::with(['matieres' => function($q) use ($matieres) {
                $q->whereIn('id', $matieres->pluck('id'));
            }])->whereIn('id', $uniteIds)->get();

            $students = $classe->students;

            $releves = Releve::with(['matiere.unite', 'student.user'])->where('classe', $id)->get();
            foreach ($students as $student) {
                $user = User::find($student->user);
                if (!$user) {
                    continue;
                }

                $note = [];
                $count_non_validated = 0;
                $count_validated = 0;
                $note['name'] = $user->firstname . ' ' . $user->lastname;
                $note['user'] = $user;
                $note['notes'] = [];

                $somme_notes = 0;
                $somme_coeffs = 0;

                foreach ($releves as $releve) {
                    if ($releve->student == $student->id) {
                        Log::info("Relevé " . $releve);

                        // Calcul de la moyenne de la matière
                        $moyenne = ($releve->exam1 + $releve->exam2 + $releve->partial) / 3;
                        if ($releve->remedial) {
                            $moyenne = $releve->remedial;
                        }
                        $moyenne = round($moyenne, 2);

                        if ($moyenne < 10) {
                            $count_non_validated++;
                        } else {
                            $count_validated++;
                        }
                        Log::info("Moyenne pour le relevé " . $moyenne);

                        $matiere = Matiere::find($releve->matiere);
                        if (!$matiere) {
                            Log::info("Matière non trouvée pour le relevé " . $releve->id);
                            continue;
                        }
                        Log::info("Matiere en question et code: " . $matiere->code);

                        $note['notes'][$matiere->code] = $moyenne;

                        // Pour la moyenne générale
                        $somme_notes += $moyenne * $matiere->coefficient;
                        $somme_coeffs += $matiere->coefficient;
                    }
                }

                // Moyenne générale pondérée
                $note['moyenne'] = $somme_coeffs > 0 ? round($somme_notes / $somme_coeffs, 2) : null;

                // Décision
                $note['cote'] = $this->getCote($note['moyenne']);
                $note['decision'] = $count_non_validated > 0
                    ? $count_non_validated . ' ECUE non validées'
                    : 'Validé';
                $note['count_non_validated'] = $count_non_validated;
                $note['count_validated'] = $count_validated;

                $notes[] = $note;
            }
            
            $meansPerMatiere = $this->generalMeanPerMatiere($notes, $matieres);

            $cycle   = Cycle::findOrFail($classe->cycle);
            $filiere = Filiere::findOrFail($classe->filiere);

            $relevesNotesController = new ReleveNotesController();
            $pdfresponse = $relevesNotesController($cycle, $filiere, $classe, $unites, $notes, $meansPerMatiere);

            return response()->json([
                'success' => true,
                'message' => 'Releve generated successfully',
                'data'    => $notes,
                'pdfresponse' => $pdfresponse
            ], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Releve not found: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Releve not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);
        } catch (\Exception $e) {
            Log::error("Failed to generate releve: " . $e->getMessage());
            Log::error("Error details: " . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve releve',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Calcule la moyenne générale par matière
     */
    private function generalMeanPerMatiere(array $notes, $matieres)
    {
        $means = [];
        foreach ($matieres as $matiere) {
            $code = $matiere->code;
            $total = 0;
            $count = 0;
            foreach ($notes as $note) {
                if (isset($note['notes'][$code])) {
                    $total += $note['notes'][$code];
                    $count++;
                }
            }
            $means[$code] = $count > 0 ? round($total / $count, 2) : null;
        }
        return $means;
    }

    /**
     * Détermine la cote en fonction de la moyenne
     */
    private function getCote($moyenne)
    {
        $moyenne *= 5;
        if (is_null($moyenne)) return null;
        if ($moyenne >= 90) return 'A';
        if ($moyenne >= 80) return 'B';
        if ($moyenne >= 70) return 'C';
        if ($moyenne >= 60) return 'D';
        if ($moyenne >= 50) return 'E';
        return 'F';
    }




    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $releve = Releve::findOrFail($id);
            $releve = Releve::with(['matiere', 'student'])->findOrFail($releve->id);

            return response()->json([
                'success' => true,
                'message' => 'Releve retrieved successfully',
                'data'    => $releve,
            ], 200);
 
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Releve not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve releve',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Releve $releve)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReleveRequest $request, $id)
    {
        try {
            $releve = Releve::findOrFail($id);

            $releve->update($request->validated());
            $releve = Releve::with(['matiere', 'student'])->findOrFail($releve->id);

            return response()->json([
                'success' => true,
                'message' => 'Releve updated successfully',
                'data'    => $releve,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Releve not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update releve',
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
            $releve = Releve::findOrFail($id);

            $releve->delete();

            return response()->json([
                'success' => true,
                'message' => 'Releve deleted successfully',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Releve not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete releve',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}
