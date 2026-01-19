<?php

namespace App\Http\Controllers\API;


use App\DTO\Product\ProductFilterDTO;
use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, ProductService $service): JsonResponse
    {
        $filters = ProductFilterDTO::from($request->query());

        return response()->json(
            $service->getPaginateProducts($filters)
        );
    }
}
