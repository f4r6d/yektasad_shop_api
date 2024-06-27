<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: [
                'logout',
            ]),
        ];
    }

    /**
     * @OA\Post(
     *      path="/api/auth/register",
     *      operationId="register",
     *      tags={"auth"},
     *      summary="register",
     *      description="register user",
     *          @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              required={"name", "email", "password", "password_confirmation"},
     *                 schema="Request",
     *                 title="register",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Farshid"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     example="farshid@gmail.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     example="12345678",
     *                     format="password"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     example="12345678",
     *                     format="password"
     *                 )
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Response Message",
     *       ),
     *     )
     */
    public function register(RegisterUserRequest $request)
    {
        $user = User::create($request->only('name', 'email', 'password'));
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => 'Bearer ' . $token]);
    }


    /**
     * @OA\Post(
     *      path="/api/auth/login",
     *      operationId="login",
     *      tags={"auth"},
     *      summary="Login",
     *      description="Login",
     *          @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              required={"email","password"},
     *                 schema="Request",
     *                 title="login",
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     example="farshid@gmail.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     example="12345678",
     *                     format="password"
     *                 )
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Response Message",
     *       ),
     *     )
     */
    public function login(LoginUserRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => 'Bearer ' . $token]);
    }

    /**
     * @OA\Get(
     *      path="/api/auth/logout",
     *      operationId="logout",
     *      tags={"auth"},
     *      summary="Logout",
     *      description="Logout",
     *      security={
     *           {"api_token": {}}
     *       },
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       )
     * )
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
