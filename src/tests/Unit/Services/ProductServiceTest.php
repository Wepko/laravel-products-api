<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Product;
use App\Services\ProductService;
use App\DTOs\Product\ProductFilterDTO;
use App\DTOs\Product\ProductDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testServiceReturnsPaginatedProductDTOs(): void
    {
        Product::factory()->count(5)->create();

        $service = app(ProductService::class);
        $filters = new ProductFilterDTO();

        /** @var ProductService $service */
        $result = $service->getPaginateProducts($filters);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertInstanceOf(ProductDTO::class, $result->items()[0]);
    }
}
