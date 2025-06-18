<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductApiController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Barcha mahsulotlar ro'yxati",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Mahsulotlar muvaffaqiyatli qaytarildi"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Product::all());
    }

    
    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Bitta mahsulotni ko‘rish",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Mahsulot topildi"),
     *     @OA\Response(response=404, description="Topilmadi")
     * )
     */
    public function show($id)
    {
        $product = Product::find($id);
        return $product
            ? response()->json($product)
            : response()->json(['message' => 'Topilmadi'], 404);
    }


    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Yangi mahsulot yaratish",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price", "category_id"},
     *             @OA\Property(property="name", type="string", example="MacBook M4"),
     *             @OA\Property(property="description", type="string", example="Yangi M4 chipli MacBook"),
     *             @OA\Property(property="price", type="number", example=3000.00),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="image", type="string", example="products/macbook.jpg")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Mahsulot yaratildi"),
     *     @OA\Response(response=422, description="Validatsiya xatoliklari")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|string',
        ]);

        $product = Product::create($data);
        return response()->json($product, 201);
    }


    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Mahsulotni yangilash",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="MacBook Pro"),
     *             @OA\Property(property="description", type="string", example="Yangilangan model"),
     *             @OA\Property(property="price", type="number", example=3500.00),
     *             @OA\Property(property="category_id", type="integer", example=2),
     *             @OA\Property(property="image", type="string", example="products/macbook-pro.jpg")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Mahsulot yangilandi"),
     *     @OA\Response(response=404, description="Topilmadi")
     * )
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Topilmadi'], 404);

        $data = $request->validate([
            'name' => 'string',
            'description' => 'nullable|string',
            'price' => 'numeric',
            'category_id' => 'exists:categories,id',
            'image' => 'nullable|string',
        ]);

        $product->update($data);
        return response()->json($product);
    }


    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Mahsulotni o‘chirish",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="O‘chirildi"),
     *     @OA\Response(response=404, description="Topilmadi")
     * )
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Topilmadi'], 404);

        $product->delete();
        return response()->json(['message' => 'O‘chirildi']);
    }



    /**
 * @OA\Get(
 *     path="/api/products/search",
 *     summary="Mahsulotlar ro'yxatini olish (qidirish bilan)",
 *     tags={"Mahsulotlar"},
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Mahsulot nomi yoki tavsifi bo‘yicha qidirish",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Mahsulotlar ro'yxati",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="iPhone 13"),
 *                     @OA\Property(property="description", type="string", example="Eng yangi iPhone modeli"),
 *                     @OA\Property(property="price", type="number", format="float", example=999.99),
 *                     @OA\Property(property="image", type="string", example="products/iphone13.jpg"),
 *                     @OA\Property(property="category_id", type="integer", example=2),
 *                     @OA\Property(property="created_at", type="string", example="2025-06-15T12:00:00Z"),
 *                     @OA\Property(property="updated_at", type="string", example="2025-06-15T12:00:00Z"),
 *                 )
 *             )
 *         )
 *     )
 * )
 */
    public function search(Request $request): JsonResponse
{
    $search = $request->query('search');

    $products = Product::query()
        ->when($search, fn ($query) =>
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
        )
        ->latest()
        ->get();

    return response()->json([
        'status' => true,
        'data' => $products,
    ]);
}


}
