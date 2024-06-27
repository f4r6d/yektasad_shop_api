<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;

class CartController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: [
                'addToCart',
                'removeFromCart',
                'viewCart',
            ]),
        ];
    }


    /**
     * @OA\Post(
     *      path="/api/cart/add",
     *      operationId="add",
     *      tags={"cart"},
     *      summary="add to cart",
     *      description="add to cart",
     *      security={
     *           {"api_token": {}}
     *       },
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              required={"product_id"},
     *                 schema="Request",
     *                 title="add",
     *                 @OA\Property(
     *                     property="product_id",
     *                     type="number",
     *                     example="1"
     *                 )
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       )
     * )
     */
    public function addToCart(Request $request)
    {
        DB::begintransaction();

        $request->validate([
            'product_id' => 'required|exists:products,id',
            // 'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $product = Product::find($request->product_id);

        if ($product->quantity < 1) {
            return response()->json(['message' => 'Not enough product in stock'], 400);
        }

        $existingItem = $cart->items()->where('product_id', $request->product_id)->first();
        if ($existingItem) {
            return response()->json(['message' => 'Product already in cart'], 400);
        }

        $cart->items()->create([
            'product_id' => $request->product_id,
            // 'quantity' => $request->quantity,
        ]);

        $product->decrement('quantity');

        DB::commit();

        return response()->json($cart->load('items.product'), 201);
    }

    /**
     * @OA\Delete(
     *      path="/api/cart/remove/{item_id}",
     *      operationId="remove",
     *      tags={"cart"},
     *      summary="remove from cart",
     *      description="remove from cart",
     *      security={
     *           {"api_token": {}}
     *       },
     *      @OA\Parameter(
     *          name="item_id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="number",
     *              format="int"
     *          ),
     *          description="The item id to be deleted"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       )
     * )
     */
    public function removeFromCart(Request $request, $itemId)
    {
        DB::beginTransaction();

        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
        $item = $cart->items()->find($itemId);
        if (!$item) {
            return response()->json(['message' => 'Item is not in your cart; wrong item_id'], 400);
        }
        $item->product->increment('quantity');
        $item->delete();

        DB::commit();

        return response()->json($cart->load('items.product'));
    }


    /**
     * @OA\Get(
     *      path="/api/cart",
     *      operationId="cart.index",
     *      tags={"cart"},
     *      summary="cart.index",
     *      description="cart.index",
     *      security={
     *           {"api_token": {}}
     *       },
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       )
     * )
     */
    public function viewCart(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)->with('items.product')->first();

        return response()->json($cart);
    }
}
