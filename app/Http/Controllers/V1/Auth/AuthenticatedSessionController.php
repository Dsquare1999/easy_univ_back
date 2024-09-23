<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\UpdateProfileRequest;
use App\Http\Requests\V1\Auth\UpdateCoverRequest;

use Carbon\Carbon;

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

            $user->name = $request->input('name', $user->name);
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
}
