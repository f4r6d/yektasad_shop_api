<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="YektaShop",
 *      description="YektaShop API Docs",
 *      @OA\Contact(
 *          email="farshidelf@yahoo.com"
 *      )
 * ),
 * @OA\SecurityScheme(
 *         type="apiKey",
 *         description="Bearer {token}",
 *         name="Authorization",
 *         in="header",
 *         securityScheme="api_token"
 *     )
 */
abstract class Controller
{
    //
}
