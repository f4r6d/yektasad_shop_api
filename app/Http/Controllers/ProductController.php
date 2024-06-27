<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/products",
     *      operationId="products.index",
     *      tags={"products"},
     *      summary="products.index",
     *      description="products.index",
     *      @OA\Response(
     *          response=200,
     *          description="Response Message",
     *       ),
     *     )
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products, 200);
    }


    /**
     * @OA\Post(
     *      path="/api/products",
     *      operationId="products.store",
     *      tags={"products"},
     *      summary="products.store",
     *      description="products.store",
     *          @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *              required={"name", "price", "quantity"},
     *                 schema="Request",
     *                 title="products.store",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Book"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="number",
     *                     example="30000"
     *                 ),
     *                 @OA\Property(
     *                     property="quantity",
     *                     type="number",
     *                     example="3"
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
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->only(
            'name',
            'price',
            'quantity',
        ));

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
