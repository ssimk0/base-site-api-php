<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPassword;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|max:32'
        ]);

        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function userInfo(): JsonResponse
    {
        $user = auth()->user();

        return response()->json($user);
    }

    /**
     * @return JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json([], 404);
        }
        $token = Str::random(60);

        // create a new token to be sent to the user.
        DB::table('password_resets')->updateOrInsert([
            'email' => $request->email,
            'token' => $token
        ]);

        Mail::to($user)->send(new ForgotPassword($token));

        return $this->successResponse();
    }

    public function resetPassword(Request $request, $token): JsonResponse
    {
        $data = $request->validate([
            'password' => 'required|min:6|max:32|confirmed'
        ]);

        $tokenData = DB::table('password_resets')
        ->where('token', $token)->first();

        if (!$tokenData) {
            return response()->json([], 404);
        }

        $user = User::where('email', $tokenData->email)->first();
        if (!$user) {
            return response()->json([], 404);
        }

        $user->password = Hash::make($data['password']);
        $user->update();

        Auth::login($user);

        DB::table('password_resets')->where('email', $user->email)->delete();

        return $this->successResponse();
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(Auth::refresh());
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'first_name' => 'required|min:3|string|max:255',
            'last_name' => 'required|min:3|string|max:255',
            'password' => 'required|min:6|confirmed|max:32',
        ]);

        if (User::where('email', $data['email'])->exists()) {
            return response()->json([], 400);
        }

        $data['password'] = Hash::make($data['password']);

        try {
            User::create($data);
        } catch (\Exception $e) {
            $this->logDebug('Error while registration user: ' . $e->getMessage());
            return response()->json([], 500);
        }
        return $this->successResponse([], 201);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}
