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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage; 

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\UploadedFile;

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

    /**
     * Download in storage
     */
    public function download($classeId, $matiereId)
    {
        try {
            // Récupérer la classe avec les étudiants et la matière
            $classe = Classe::with(['students.user'])->findOrFail($classeId);
            $matiere = Matiere::findOrFail($matiereId);

            // Créer un nouveau spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // En-têtes
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Nom Complet');
            $sheet->setCellValue('C1', 'Devoir 1');
            $sheet->setCellValue('D1', 'Devoir 2');
            $sheet->setCellValue('E1', 'Partiel');
            $sheet->setCellValue('F1', 'Rattrapage');

            // Style des en-têtes
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center'],
                'borders' => [
                    'allBorders' => ['borderStyle' => 'thin']
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'E0E0E0']
                ]
            ];

            $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

            // Remplir les données des étudiants
            $row = 2;
            foreach ($classe->students as $student) {
                
                $user = User::find($student->user);
                // ID court (5 premiers caractères)
                $shortId = substr($user->id, 0, 5);

                // Nom complet
                $fullName = $user->firstname . ' ' . $user->lastname;

                $sheet->setCellValue('A' . $row, $shortId);
                $sheet->setCellValue('B' . $row, $fullName);

                // Récupérer les notes existantes
                $releve = Releve::where('student', $student->id)
                    ->where('matiere', $matiereId)
                    ->first();

                if ($releve) {
                    $sheet->setCellValue('C' . $row, $releve->exam1 ?? '');
                    $sheet->setCellValue('D' . $row, $releve->exam2 ?? '');
                    $sheet->setCellValue('E' . $row, $releve->partial ?? '');
                    $sheet->setCellValue('F' . $row, $releve->remedial ?? '');
                }

                $row++;
            }

            // Ajuster la largeur des colonnes
            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(30);
            foreach(range('C', 'F') as $col) {
                $sheet->getColumnDimension($col)->setWidth(15);
            }

            // Protection de la feuille sauf les cellules de notes
            $sheet->getProtection()->setSheet(true);
            $sheet->getStyle('C2:F' . ($row-1))->getProtection()
                ->setLocked(false);

            // Nom du fichier
            $filename = "notes_template_" . $classeId . "_" . $matiereId . ".xlsx";
            $path = Storage::disk('local')->path($filename);

            $writer = new Xlsx($spreadsheet);
            $writer->save($path);

            // 🔥 Retourner le fichier en réponse HTTP (stream binaire)
            return response()->download($path, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du template',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function import(Request $request, $classeId, $matiereId)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls'
            ]);

            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Supprimer l'en-tête
            array_shift($rows);

            $updates = 0;
            $errors = [];
            $processedRows = 0;

            // Valider et traiter chaque ligne
            foreach ($rows as $rowIndex => $row) {
                $processedRows++;
                if (empty($row[0])) continue; // Ignorer les lignes vides

                $shortId = $row[0];
                $user = User::where('id', 'LIKE', $shortId . '%')->first();

                if (!$user) {
                    $errors[] = "Ligne {$processedRows}: ID utilisateur non trouvé ({$shortId})";
                    continue;
                }

                $student = Student::where('user', $user->id)
                                ->where('classe', $classeId)
                                ->first();

                if (!$student) {
                    $errors[] = "Ligne {$processedRows}: Étudiant non trouvé pour l'utilisateur {$user->firstname} {$user->lastname}";
                    continue;
                }

                // Préparer les données à mettre à jour
                $updateData = [];

                // Mapper les colonnes (ignorer la colonne 1 qui est le nom)
                $columnMapping = [
                    2 => 'exam1',
                    3 => 'exam2',
                    4 => 'partial',
                    5 => 'remedial'
                ];

                foreach ($columnMapping as $excelColumn => $dbColumn) {
                    if (isset($row[$excelColumn]) && $row[$excelColumn] !== '') {
                        $note = is_numeric($row[$excelColumn]) ? (float) $row[$excelColumn] : null;
                        
                        // Valider la note
                        if ($note !== null) {
                            if ($note < 0 || $note > 20) {
                                $errors[] = "Ligne {$processedRows}: Note invalide ({$note}) pour {$user->firstname} {$user->lastname}";
                                continue 2; // Passer à l'étudiant suivant
                            }
                            $updateData[$dbColumn] = $note;
                        }
                    }
                }

                // Mettre à jour ou créer le relevé
                if (!empty($updateData)) {
                    $releve = Releve::updateOrCreate(
                        [
                            'student' => $student->id,
                            'matiere' => $matiereId,
                            'classe' => $classeId
                        ],
                        $updateData
                    );
                    $updates++;
                }
            }

            // Préparer la réponse
            $response = [
                'success' => empty($errors),
                'message' => "Import terminé. {$updates} relevés mis à jour.",
                'data' => [
                    'updates' => $updates,
                    'processed_rows' => $processedRows,
                    'errors' => $errors
                ]
            ];

            // Si des erreurs ont été trouvées mais certaines mises à jour ont réussi
            if (!empty($errors) && $updates > 0) {
                return response()->json($response, 207); // Status 207 Multi-Status
            }

            // Si des erreurs ont été trouvées et aucune mise à jour n'a réussi
            if (!empty($errors) && $updates === 0) {
                return response()->json($response, 422); // Status 422 Unprocessable Entity
            }

            // Si tout s'est bien passé
            return response()->json($response, 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import des notes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import du fichier',
                'error' => $e->getMessage()
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
    public function generate($id, $year_part)
    {
        try {
            $notes = [];
            $classe = Classe::with(['matieres.unite'])->findOrFail($id);
            $matieres = $classe->matieres;
            Log::info("Matieres: " . $matieres);
            $matieres = $matieres->where('year_part', $year_part);
            
            $uniteIds = $matieres->pluck('unite')->filter()->unique()->values()->all();

            Log::info("Unite IDs: " . implode(', ', $uniteIds));
            $unites = Unite::with(['matieres' => function($q) use ($matieres) {
                $q->whereIn('id', $matieres->pluck('id'));
            }])->whereIn('id', $uniteIds)->orderBy('code', 'asc')->get();

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
                $note['student'] = $student;
                $note['notes'] = [];

                $somme_notes = 0;
                $somme_coeffs = 0;

                foreach ($releves as $releve) {
                    if ($releve->student == $student->id) {
                        // Calcul de la moyenne de la matière
                        $moyenne = ((($releve->exam1 + $releve->exam2) / 2) * 0.4) + ($releve->partial * 0.6);
                        if ($releve->remedial) {
                            $moyenne = $releve->remedial;
                        }
                        $moyenne = round($moyenne, 2);

                        if ($moyenne < 10) {
                            $count_non_validated++;
                        } else {
                            $count_validated++;
                        }
                        $matiere = Matiere::find($releve->matiere);
                        if (!$matiere) {
                            continue;
                        }
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
            $qrCodePath = $this->generateQrCode("Bulletin émis le ".now()->format('d/m/Y'), "Bulletin QR Code");

            $relevesNotesController = new ReleveNotesController();
            $pdfresponse = $relevesNotesController($cycle, $filiere, $classe, $unites, $notes, $meansPerMatiere, $year_part, $somme_coeffs, $qrCodePath);

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
        if (is_null($moyenne)) return null;
        if ($moyenne >= 16) return 'A';
        if ($moyenne >= 14) return 'B';
        if ($moyenne >= 12) return 'C';
        if ($moyenne >= 10) return 'D';
        return 'E';
    }

    private function generateQrCode(string $data, string $filename): ?string
    {
        try {
            $qrData = urlencode($data);
            $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={$qrData}";
            $qrCodePngData = @file_get_contents($apiUrl);

            if ($qrCodePngData === false) {
                throw new \Exception("Impossible de contacter l'API du QR Code");
            }

            $qrCodeStoragePath = 'qrcodes/' . $filename;
            Storage::disk('public')->put($qrCodeStoragePath, $qrCodePngData);
            
            $absolutePath = Storage::disk('public')->path($qrCodeStoragePath);
            return $absolutePath;

        } catch (\Exception $e) {
            Log::error("Erreur génération QR Code: " . $e->getMessage());
            return null;
        }
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
