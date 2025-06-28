<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    use HttpResponses;
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        // Validate the request
        $request->authenticate();

        // Get the authenticated user
        $user = Auth::user();
        $token = $user->createToken('auth_token' . $user->first_name)->plainTextToken;

        // Return success response with user and token
        return $this->success([
            'user' => $user,
            'token' => $token,
        ], 'Login successful');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        // Delete the current access token
        $request->user()->currentAccessToken()->delete();

        // Return success response
        return $this->success(null, 'Logout successful');
    }

    public function refresh(Request $request)
    {
        // delete the current token
        $request->user()->currentAccessToken()->delete();

        // create a new token
        $token = $request->user()->createToken("auth_token" . $request->user()->first_name)->plainTextToken;

        return $this->success([
            'token' => $token,
        ], 'token refreshed successfully');
    }
}
