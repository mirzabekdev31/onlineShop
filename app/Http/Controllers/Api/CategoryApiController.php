<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryApiController extends Controller
{


    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Barcha kategoriyalar ro'yxati",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Kategoriyalar muvaffaqiyatli qaytarildi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Elektronika"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-17T10:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-17T10:00:00Z")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $categories = Category::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $categories,
        ]);
    }
}
