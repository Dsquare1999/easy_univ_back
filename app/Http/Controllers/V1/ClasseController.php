<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Models\Classe;
use App\Models\Filiere;
use App\Models\Cycle;
use App\Models\Student;
use App\Models\Unite;
use App\Models\Releve;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreClasseRequest;
use App\Http\Requests\V1\UpdateClasseRequest;

use Illuminate\Support\Facades\Storage; 
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Tag;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Auth;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            // $user = Auth()->user();
            
            // $my_class_ids = Student::where('user', $user->id)->pluck('classe')->toArray();
            $classes = Classe::all();

            // $classes->transform(function ($classe) use ($my_class_ids) {
            //     $classe->registered = in_array($classe->id, $my_class_ids);
            //     return $classe;
            // });

            return response()->json([
                'success' => true,
                'message' => 'Classes retrieved successfully',
                'data'    => $classes,
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
            $classe = Classe::findOrFail($classe->id);

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
     * Download in storage
     */
    public function download($classeId)
    {
        try {
            // Récupérer la classe avec les étudiants
            $classe = Classe::with(['students.user'])->findOrFail($classeId);

            // Créer un nouveau spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // En-têtes
            $sheet->setCellValue('A1', 'Nom');
            $sheet->setCellValue('B1', 'Prénoms');
            $sheet->setCellValue('C1', 'Sexe');
            $sheet->setCellValue('D1', 'Matricule');
            $sheet->setCellValue('E1', 'E-mail');
            $sheet->setCellValue('F1', 'Téléphone');
            $sheet->setCellValue('G1', 'Titre');
            $sheet->setCellValue('H1', 'Nationalité');
            $sheet->setCellValue('I1', 'Date de Naissance');
            $sheet->setCellValue('J1', 'Lieu de Naissance');
            $sheet->setCellValue('K1', 'Adresse');

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

            $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

            // Remplir les données des étudiants
            $row = 2;
            foreach ($classe->students as $student) {
                $user = User::find($student->user);
                $sheet->setCellValue('A' . $row, $user->lastname);
                $sheet->setCellValue('B' . $row, $user->firstname);
                $sheet->setCellValue('C' . $row, $user->sexe);
                $sheet->setCellValue('D' . $row, $user->matricule);
                $sheet->setCellValue('E' . $row, $user->email);
                $sheet->setCellValue('F' . $row, $user->phone);
                $sheet->setCellValue('G' . $row, $student->titre);
                $sheet->setCellValue('H' . $row, $user->nationality);
                $sheet->setCellValue('I' . $row, $user->birthdate);
                $sheet->setCellValue('J' . $row, $user->birthplace);
                $sheet->setCellValue('K' . $row, $user->address);
                $row++;
            }

            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Ajuster la largeur des colonnes
            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(10);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(20);
            $sheet->getColumnDimension('K')->setWidth(40);

            $sheet->getProtection()
                ->setSheet(true)
                ->setPassword('LePhenix2025');

            // $sheet->getProtection()->setSheet(true);
            $sheet->getStyle('A:K')->getProtection()
                ->setLocked(Protection::PROTECTION_UNPROTECTED);
            $sheet->getStyle('L:XFD')->getProtection()
            ->setLocked(Protection::PROTECTION_PROTECTED);

            // $sheet->getStyle('A2:K' . ($row-1))->getProtection()
            //     ->setLocked(false);

            // Nom du fichier
            $filename = "etudiants_template_" . $classeId . ".xlsx";
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

    public function import(Request $request, $classeId)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls'
            ]);

            $classe = Classe::findOrFail($classeId);
            $tag = Tag::where('fee', 0)->first();

            if (!$tag) {
                throw new \Exception("Tag avec frais 0 non trouvé");
            }

            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            array_shift($rows);

            $stats = [
                'users_created' => 0,
                'users_updated' => 0,
                'students_created' => 0,
                'errors' => []
            ];

            // Traiter chaque ligne
            foreach ($rows as $row) {
                if (empty($row[4])) {
                    continue;
                }

                $rawBirthdate = $row[8] ?? null;
                $birthdate = null;

                if (!empty($rawBirthdate)) {
                    try {
                        if (is_numeric($rawBirthdate)) {
                            $birthdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawBirthdate)
                                ->format('Y-m-d');
                        } else {
                            $parsed = \DateTime::createFromFormat('m/d/Y', $rawBirthdate)
                                ?: \DateTime::createFromFormat('d/m/Y', $rawBirthdate)
                                ?: \DateTime::createFromFormat('Y-m-d', $rawBirthdate);

                            if ($parsed) {
                                $birthdate = $parsed->format('Y-m-d');
                            } else {
                                throw new \Exception("Format de date invalide : $rawBirthdate");
                            }
                        }
                    } catch (\Throwable $th) {
                        throw new \Exception("Impossible de lire la date de naissance : $rawBirthdate");
                    }
                }


                try {
                    // Préparer les données utilisateur
                    $userData = [
                        'firstname' => $row[1] ?? '',
                        'lastname' => $row[0] ?? '',
                        'sexe' => $row[2] ?? '',
                        'matricule' => $row[3] ?? '',
                        'email' => $row[4],
                        'phone' => $row[5] ?? '',     // Téléphone
                        'nationality' => $row[7] ?? '', // Nationalité
                        'birthdate' => $birthdate, // Date de naissance
                        'birthplace' => $row[9] ?? '', // Lieu de naissance
                        'address' => $row[10] ?? '',    // Adresse
                        'type' => 0
                    ];

                    // Créer ou mettre à jour l'utilisateur
                    $user = User::updateOrCreate(
                        ['email' => $userData['email']],
                        $userData
                    );

                    // Vérifier si l'étudiant existe déjà dans cette classe
                    $existingStudent = Student::where('user', $user->id)
                        ->where('classe', $classeId)
                        ->first();

                    if (!$existingStudent) {
                        // Créer l'étudiant
                        $student = Student::create([
                            'user' => $user->id,
                            'classe' => $classeId,
                            'tag' => $tag->id,
                            'titre' => $row[6] ?? 'ATP', // Titre par défaut
                            'statut' => 'PRE-INSCRIT'
                        ]);

                        // Créer les relevés pour chaque matière
                        foreach ($classe->matieres as $matiere) {
                            Releve::create([
                                'student' => $student->id,
                                'matiere' => $matiere->id,
                                'classe' => $classeId,
                            ]);
                        }

                        $stats['students_created']++;
                    }

                    if ($user->wasRecentlyCreated) {
                        $stats['users_created']++;
                    } else {
                        $stats['users_updated']++;
                    }

                } catch (\Exception $e) {
                    $stats['errors'][] = "Erreur ligne " . $row[4] . ": " . $e->getMessage();
                    continue;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Import terminé avec succès',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $user = Auth()->user();
            
            $my_class_ids = Student::where('user', $user->id)->pluck('classe')->toArray();
            $classe = Classe::findOrFail($id);

            $classe->registered = in_array($classe->id, $my_class_ids);

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
            $classe = Classe::findOrFail($id);

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


    /**
     * Promote students to next year
     * @param string $id ID of the current class
     */
    public function promote($id)
    {
        try {
            // Récupérer la classe actuelle avec ses relations
            $currentClasse = Classe::with(['filiere', 'cycle', 'students.user'])->findOrFail($id);
            
            // Vérifier si une classe pour l'année suivante existe déjà
            $nextYear = $currentClasse->academic_year + 1;
            $nextClassYear = $currentClasse->year + 1;
            
            $existingNextClass = Classe::where('filiere', $currentClasse->filiere)
                ->where('cycle', $currentClasse->cycle)
                ->where('year', $nextClassYear)
                ->where('academic_year', $nextYear)
                ->first();

            if ($existingNextClass) {
                throw new \Exception('Une classe pour l\'année suivante existe déjà');
            }

            // Créer la nouvelle classe
            $newClasse = Classe::create([
                'filiere' => $currentClasse->filiere,
                'cycle' => $currentClasse->cycle,
                'year' => $nextClassYear,
                'academic_year' => $nextYear,
                'parts' => $currentClasse->parts,
                'status' => 0
            ]);

            $studentsPromoted = 0;
            $errors = [];

            // Pour chaque étudiant de la classe actuelle
            foreach ($currentClasse->students as $student) {
                try {
                    $newStudent = Student::create([
                        'user' => $student->user,
                        'classe' => $newClasse->id,
                        'tag' => $student->tag,
                        'titre' => $student->titre,
                        'statut' => 'EN ATTENTE'
                    ]);

                    // Créer les relevés pour les matières (s'il y en a)
                    foreach ($newClasse->matieres as $matiere) {
                        Releve::create([
                            'student' => $newStudent->id,
                            'matiere' => $matiere->id,
                            'classe' => $newClasse->id
                        ]);
                    }

                    $studentsPromoted++;
                } catch (\Exception $e) {
                    $errors[] = "Erreur lors de la promotion de l'étudiant {$student->user}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Classe clôturée et étudiants promus avec succès',
                'data' => [
                    'new_class' => $newClasse,
                    'students_promoted' => $studentsPromoted,
                    'errors' => $errors
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la clôture de la classe',
                'errors' => ['message' => $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Export student list as PDF
     */
    public function export($id)
    {
        try {
            $classe = Classe::with(['filiere', 'cycle'])->findOrFail($id);
            \Log::info('Class Object', $classe->toArray());
            
            $filiere = Filiere::find($classe->filiere);
            $cycle = Cycle::find($classe->cycle);

            \Log::info('Cycle Object', $cycle->toArray());
            $students = Student::where('classe', $id)
                ->with('user')
                ->get();

            \Log::info('Students Object', $students->toArray());
            $students_data = [];
            foreach ($students as $student) {
                $user = User::find($student->user);
                $students_data[] = [
                    'id' => $student->id,
                    'titre' => $student->titre,
                    'lastname' => $user ? $user->lastname : null,
                    'firstname' => $user ? $user->firstname : null,
                    'matricule' => $user ? $user->matricule : null,
                    'phone' => $user ? $user->phone : null,
                    'email' => $user ? $user->email : null,
                    'sexe' => $user ? $user->sexe : null
                ];
            }
            \Log::info('Filtered students data array', $students_data);

            // Créer le PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('classes.student-list-export', [
                'classe' => $classe,
                'filiere' => $filiere,
                'cycle' => $cycle,
                'students' => $students_data
            ]);

            // Configurer le PDF
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);

            // Nom du fichier
            $filename = 'Liste_Etudiants_' .$filiere->name . '_' . $classe->year . 'A_'. $classe->academic_year . '.pdf';

            // Retourner le PDF pour téléchargement
            return $pdf->download($filename);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Classe not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export student list',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}