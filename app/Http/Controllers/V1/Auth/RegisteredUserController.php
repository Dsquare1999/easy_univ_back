<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $response = [
                        'success'       => false,
                        'message'       => 'Registration Failed',
                        'data'          => [],
                        'errors'        => []
                    ];
        $responseCode = 500;
                    
                    
        try {
            $validatedData = $request->validated();
            $validatedData['password'] = Hash::make($validatedData['password']);

            $user = User::create($validatedData);

            if(!$user) {
                $response['errors'] = [
                    'message' => "Erreur Serveur"
                ];
                return response()->json($response, $responseCode);
            }
            
            event(new Registered($user));
            
            $response['success'] = true;
            $response['message'] = 'Registration Successful';
            $responseCode = 201;

            return response()->json($response, $responseCode);

        } catch (\Throwable $th) {
            $response['errors'] = [
                'message' => $th->getMessage()
            ];
            return response()->json($response, $responseCode);
        }
        
    }
}
