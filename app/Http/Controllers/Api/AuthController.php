<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{


    /**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Foydalanuvchini ro'yxatdan o'tkazish",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string", example="Mirzabek"),
 *             @OA\Property(property="email", type="string", format="email", example="mirzabek@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="secret123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Foydalanuvchi yaratildi"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validatsiya xatosi"
 *     )
 * )
 */
    public function register(Request $request)
{
    try {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('main')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Xatolik!',
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ], 500);
    }
}


    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Foydalanuvchi login qiladi",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="mirzabek@example.com"),
     *             @OA\Property(property="password", type="string", example="secret123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Muvaffaqiyatli login"),
     *     @OA\Response(response=401, description="Email yoki parol noto‘g‘ri")
     * )
     */

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Email yoki parol noto‘g‘ri'], 401);
        }

        $token = $user->createToken('main')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }



    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Foydalanuvchi sessiyasini yopadi",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Chiqildi")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Chiqildi']);
    }


    /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Hozirgi foydalanuvchi ma'lumotlari",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Foydalanuvchi ma'lumotlari")
     * )
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
