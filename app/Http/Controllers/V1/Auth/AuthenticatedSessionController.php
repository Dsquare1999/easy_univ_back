<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\UpdateProfileRequest;
use App\Http\Requests\V1\Auth\UpdateCoverRequest;

use Carbon\Carbon;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


enum TokenAbility: string
{
    case ISSUE_ACCESS_TOKEN = 'issue-access-token';
    case ACCESS_API = 'access-api';
}

class AuthenticatedSessionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $query = User::query();

            if ($type = request()->query('type')) {
                $query->where('type', $type);
            }

            // $users = $query->paginate(8);
            $users = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data'    => $users,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $response = [
            'success'       => false,
            'message'       => 'Registration Failed',
            'data'          => [],
            'errors'        => []
        ];
        $responseCode = 500;

        try {
            $request->authenticate();
            $user = $request->user();

            $user->tokens()->delete();
            $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
            $refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));


            $response['success'] = true;
            $response['message'] = 'Login Successful';
            $response['data'] = [
                'user' => $user,
                'token' => $accessToken->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken,
            ];
            $responseCode = 200;

            return response()->json($response, $responseCode); 

        } catch (\Throwable $th) {
            $response['errors'] = [
                'message' => $th->getMessage()
            ];
            return response()->json($response, $responseCode); 
        }

        
    }

    /**
     * Handle updating the authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => 'Profile update failed',
            'data'    => [],
            'errors'  => []
        ];
        $responseCode = 500;

        try {
            $user = $request->user();

            $user->firstname = $request->input('firstname', $user->firstname);
            $user->lastname = $request->input('lastname', $user->lastname);
            $user->email = $request->input('email', $user->email);
            $user->bio = $request->input('bio', $user->bio);
            $user->phone = $request->input('phone', $user->phone);

            if ($request->hasFile('profile')) {
                if ($user->profile) {
                    Storage::disk('public')->delete($user->profile);
                }

                $path = $request->file('profile')->store('profiles', 'public');
                $user->profile = $path;
            }

            $user->save();

            $response['success'] = true;
            $response['message'] = 'Profile updated successfully';
            $response['data'] = $user;
            $responseCode = 200;

            return response()->json($response, $responseCode);

        } catch (\Throwable $th) {
            $response['errors'] = [
                'message' => $th->getMessage(),
            ];
            return response()->json($response, $responseCode);
        }
    }


    /**
     * Handle updating the authenticated user's profile.
     */
    public function updateCover(UpdateCoverRequest $request): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => 'Cover update failed',
            'data'    => [],
            'errors'  => []
        ];
        $responseCode = 500;

        try {
            $user = Auth::user();
              
            if ($request->hasFile('cover')) {
                if ($user->cover) {
                    Storage::disk('public')->delete($user->cover);
                }

                $path = $request->file('cover')->store('covers', 'public');
                $user->cover = $path;

                $user->save();

                $response['success'] = true;
                $response['message'] = 'Cover updated successfully';
                $response['data'] = $user;
                $responseCode = 200;
            }

            return response()->json($response, $responseCode);

        } catch (\Throwable $th) {
            $response['errors'] = [
                'message' => $th->getMessage(),
            ];
            return response()->json($response, $responseCode);
        }
    }

    public function turnProfessor(Request $request){
        $response = [
            'success' => false,
            'message' => 'Not turned into professor successfully',
            'data'    => [],
            'errors'  => []
        ];
        $responseCode = 500;

        try {
            $user = User::findOrFail($request->input('id'));            
            
            if($user->update(['type' => 1])){
                $response['success'] = true;
                $response['message'] = 'Turned into professor successfully';
                $response['data'] = $user;
                $responseCode = 200;
            }
            

            return response()->json($response, $responseCode);

        } catch (\Throwable $th) {
            $response['errors'] = [
                'message' => $th->getMessage(),
            ];
            return response()->json($response, $responseCode);
        }
    }

    public function turnStudent(Request $request){
        $response = [
            'success' => false,
            'message' => 'Not turned into student successfully',
            'data'    => [],
            'errors'  => []
        ];
        $responseCode = 500;

        try {
            $user = User::findOrFail($request->input('id'));

            if($user->update(['type' => 0])){
                $response['success'] = true;
                $response['message'] = 'Turned into student successfully';
                $response['data'] = $user;
                $responseCode = 200;
            }
            

            return response()->json($response, $responseCode);

        } catch (\Throwable $th) {
            $response['errors'] = [
                'message' => $th->getMessage(),
            ];
            return response()->json($response, $responseCode);
        }
    }



    /**
     * Refresh an authenticated session.
     */
    public function refreshToken(Request $request)
    {
        $accessToken = $request->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
        return response(['message' => "Token généré", 'token' => $accessToken->plainTextToken]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'errors'  => ['message' => $e->getMessage()],
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'errors'  => ['message' => $e->getMessage()],
            ], 500);
        }
    }
}
