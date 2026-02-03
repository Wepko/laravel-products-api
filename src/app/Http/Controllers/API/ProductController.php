<?php

namespace App\Http\Controllers\API;


use App\DTOs\Product\ProductFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use OpenApi\Attributes as OA;
use phpDocumentor\Reflection\Exception;

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

    public function __construct(
        private readonly  ProductService $service,
    ) {
    }

    /**
     * @param ProductFilterRequest $request
     * @return JsonResponse
     */
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
    public function index(ProductFilterRequest $request): JsonResponse
    {
        $data =  $this->service->getPaginateProducts(filters: $request->toDTO());

        return response()->json($data);
    }

    /**
     * @throws Exception
     */
    public function showBySlug(Request $request, string $slug): JsonResponse
    {
        $filters = ProductFilterDTO::from($request->query());

        $data = $this->service->getPaginateProductsBySlug(filters: $filters, type: $slug);

        return response()->json($data);
    }

}
