<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Product;
use App\Repositories\Eloquent\ProductRepository;
use App\DTOs\Product\ProductFilterDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductRepository::class);
    }

    public function testFiltersProductsByInStock(): void
    {
        Product::factory()->count(3)->create(['in_stock' => true]);
        Product::factory()->count(2)->create(['in_stock' => false]);

        $filters = new ProductFilterDTO(
            inStock: true,
        );

        $result = $this->repository->paginate($filters);

        $this->assertCount(3, $result->items());

        // Дополнительная проверка - все товары в наличии
        foreach ($result->items() as $product) {
            $this->assertTrue($product->in_stock);
        }
    }

    public function testFiltersProductsByPriceFrom(): void
    {
        Product::factory()->create(['price' => 50]);
        Product::factory()->create(['price' => 150]);

        $filters = new ProductFilterDTO(
            priceFrom: 100,  // цена от 100
        );

        $result = $this->repository->paginate($filters);

        $this->assertCount(1, $result->items());
        $this->assertEquals(150, $result->items()[0]->price);
    }

    public function testFiltersProductsByPriceTo(): void
    {
        Product::factory()->create(['price' => 50]);
        Product::factory()->create(['price' => 150]);

        $filters = new ProductFilterDTO(
            priceTo: 100,  // цена до 100
        );

        $result = $this->repository->paginate($filters);

        $this->assertCount(1, $result->items());
        $this->assertEquals(50, $result->items()[0]->price);
    }

    public function testSortsProductsByPriceDesc(): void
    {
        Product::factory()->create(['price' => 100]);
        Product::factory()->create(['price' => 300]);
        Product::factory()->create(['price' => 200]);

        $filters = new ProductFilterDTO(
            sort: 'price_desc'
        );

        $result = $this->repository->paginate($filters);

        $prices = collect($result->items())
            ->pluck('price')
            ->map(fn($price) => (float) $price)
            ->toArray();

        $this->assertEquals([300.0, 200.0, 100.0], $prices);
    }

    public function testSortsProductsByPriceAsc(): void
    {
        Product::factory()->create(['price' => 300]);
        Product::factory()->create(['price' => 100]);
        Product::factory()->create(['price' => 200]);

        $filters = new ProductFilterDTO(
            sort: 'price_asc'
        );

        $result = $this->repository->paginate($filters);

        $prices = collect($result->items())
            ->pluck('price')
            ->map(fn($price) => (float) $price)
            ->toArray();

        $this->assertEquals([100.0, 200.0, 300.0], $prices);
    }
}
