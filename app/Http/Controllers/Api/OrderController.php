<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{


    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Yangi buyurtma berish",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "quantity"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Buyurtma yaratildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Buyurtma muvaffaqiyatli qabul qilindi!"),
     *             @OA\Property(
     *                 property="order",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=5),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="product_id", type="integer", example=1),
     *                 @OA\Property(property="quantity", type="integer", example=2),
     *                 @OA\Property(property="status", type="string", example="new"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-17T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-17T12:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token talab qilinadi"),
     *     @OA\Response(response=422, description="Validatsiya xatoliklari")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'user_id' => $request->user()->id, // token orqali aniqlanadi
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'status' => 'new',
        ]);

        return response()->json([
            'message' => 'Buyurtma muvaffaqiyatli qabul qilindi!',
            'order' => $order
        ], 201);
    }




    /**
     * @OA\Get(
     *     path="/api/my-orders",
     *     summary="Foydalanuvchining barcha buyurtmalari",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Buyurtmalar ro'yxati",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=3),
     *                 @OA\Property(property="product_id", type="integer", example=1),
     *                 @OA\Property(property="quantity", type="integer", example=2),
     *                 @OA\Property(property="status", type="string", example="new"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-17T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-17T12:00:00Z"),
     *                 @OA\Property(
     *                     property="product",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="MacBook M4"),
     *                     @OA\Property(property="price", type="string", example="3000.00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token talab qilinadi")
     * )
     */
    public function myOrders(Request $request)
{
    $orders = $request->user()->orders()->with('product')->latest()->get();

    return response()->json($orders);
}

}
