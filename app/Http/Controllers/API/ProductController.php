<?php

namespace App\Http\Controllers\API;


use App\DTO\Product\ProductFilterDTO;
use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Products API'
)]
#[OA\Tag(
    name: 'Products',
    description: 'Products catalog'
)]
class ProductController extends Controller
{

    #[OA\Get(
        path: '/api/products',
        description: 'Возвращает пагинированный список товаров с возможностью фильтрации и сортировки',
        summary: 'Получить список товаров',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'filters',
                description: 'Параметры фильтрации',
                in: 'query',
                required: false,
                schema: new OA\Schema(ref: '#/components/schemas/ProductFilterDTO'),
                explode: true
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful response'
            )
        ]
    )]
    public function index(Request $request, ProductService $service): JsonResponse
    {
        $filters = ProductFilterDTO::from($request->query());

        return response()->json(
            $service->getPaginateProducts($filters)
        );
    }
}
