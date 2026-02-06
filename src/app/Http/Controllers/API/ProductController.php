<?php

namespace App\Http\Controllers\API;


use App\DTOs\Product\ProductFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Resources\ProductResource;
use App\Http\Responses\PaginatedApiResponse;
use App\Http\Responses\PaginatedResponse;
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
class ProductController extends BaseApiController
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
    /*
     * ToDo: only standard  responseJson
     *  don't use AnonymousResourceCollection!
     */
    public function index(ProductFilterRequest $request): JsonResponse
    {
        $productsPaginator = $this->service->getPaginateProducts(
            filters: $request->toDTO()
        );

        $resource = ProductResource::collection($productsPaginator);

        return (new PaginatedApiResponse(
            data: $resource,
            paginator: $productsPaginator,
            message: __('Products retrieved successfully')
        ))
            ->addMeta('api_version', 'v1')
            ->addMeta('cache', false)
            ->toResponse();
    }


    /**
     * @throws Exception
     */
    public function showBySlug(ProductFilterRequest $request, string $slug): JsonResponse
    {
        $productsPaginator = $this->service->getPaginateProductsBySlug(filters: $request->toDTO(), type: $slug);

        // $resource = ProductResource::collection($productsPaginator);

        return response()->json($productsPaginator);
    }

}
