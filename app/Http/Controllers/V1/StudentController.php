<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V1\FichePreInscriptionController;

use App\Notifications\SoumissionNotification;
use App\Notifications\InscriptionRefuseNotification;
use App\Notifications\PreinscriptionNotification;

use App\Models\Tag;
use App\Models\Classe;
use App\Models\User;
use App\Models\Student;
use App\Models\Cycle;
use App\Models\Filiere;
use Illuminate\Http\Request;
use App\Http\Requests\V1\StoreStudentRequest;
use App\Http\Requests\V1\ValidateStudentRequest;
use App\Http\Requests\V1\RefuseStudentRequest;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $students = Student::with(['classe.cycle', 'classe.filiere', 'tag', 'user'])->get();
            $tags = Tag::all();

            return response()->json([
                'success' => true,
                'message' => 'Students retrieved successfully',
                'data'    => $students,
                'tags'    => $tags
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve students',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        try {
            $validated = $request->validated();

            $validated['user'] = Auth::id();
            $student = Student::create($validated);

            $user       = User::findOrFail($student->user);
            $classe     = Classe::findOrFail($student->classe);
            $cycle      = Cycle::findOrFail($classe->cycle);
            $filiere    = Filiere::findOrFail($classe->filiere);

            $notificationPreinscription = new SoumissionNotification($student, $classe, $cycle, $filiere);
            $user->notify($notificationPreinscription);

            return response()->json([
                'success' => true,
                'message' => 'Soummission successful',
                'data'    => $student,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student for this classe',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }


    /**
     * Validate a newly created student.
     */
    public function validate(ValidateStudentRequest $request)
    {
        try {
            $validated = $request->validated();

            $student_id = $validated['student'];
            $student= Student::findOrFail($student_id);
            $tag    = Tag::findOrFail($validated['tag']);
            $titre  = $validated['titre'];

            $student->update([
                    'tag' => $tag->id,
                    'titre' => $titre,
                    'statut' => 'PRE-INSCRIT',
                ]);

            $user       = User::findOrFail($student->user);
            $classe     = Classe::findOrFail($student->classe);
            $cycle      = Cycle::findOrFail($classe->cycle);
            $filiere    = Filiere::findOrFail($classe->filiere);


            $student= Student::findOrFail($student_id);
            $fichePreInscriptionController = new FichePreInscriptionController();
            $pdfresponse = $fichePreInscriptionController($user, $student, $classe, $filiere, $cycle); 
            if($pdfresponse['success']){
                $student->update([
                    'file' => $pdfresponse['filename']
                ]);
            }

            $notificationPreinscription = new PreinscriptionNotification($student, $classe, $cycle, $filiere);
            $user->notify($notificationPreinscription);

            return response()->json([
                'success' => true,
                'message' => 'Student validated successfully',
                'data'    => $student,
                'pdfresponse' => $pdfresponse
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student for this classe',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }


    /**
     * Refuse a newly created student .
     */
    public function refuse(RefuseStudentRequest $request)
    {
        try {
            $validated = $request->validated();

            $student_id = $validated['student'];
            $why = $validated['why'];
            $student= Student::findOrFail($student_id);

            $student->update([
                    'statut' => 'REFUSE',
                ]);

            $user       = User::findOrFail($student->user);
            $classe     = Classe::findOrFail($student->classe);
            $cycle      = Cycle::findOrFail($classe->cycle);
            $filiere    = Filiere::findOrFail($classe->filiere);

            $student= Student::findOrFail($student_id);
            $inscriptionRefuseNotification = new InscriptionRefuseNotification($student, $classe, $cycle, $filiere, $why);
            $user->notify($inscriptionRefuseNotification);

            return response()->json([
                'success' => true,
                'message' => 'Student submission successfully rejected',
                'data'    => $student,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student for this classe',
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

            $classe = $id;
            $user = Auth::id();

            $student = Student::where('classe', $classe)->where('user', $user)->first();
            $student->delete();


            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}
