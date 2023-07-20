<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStore()
    {
        /** @var Product $newProduct */
        $newProduct = Product::factory()->make();

        $this
            ->postJson(route('products.store'), [
                'title' => $newProduct->title,
                'discount' => $newProduct->discount,
                'price' => (string) $newProduct->price,
            ])
            ->assertCreated()
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->where('title', $newProduct->title)
                    ->where('discount', $newProduct->discount)
                    ->where('price', (string) $newProduct->price)
                    ->etc()
            );

        $this->assertDatabaseHas(
            'products',
            [
                'title' => $newProduct->title,
                'discount' => $newProduct->discount,
                'price' => (string) $newProduct->price,
            ]
        );
    }
}
