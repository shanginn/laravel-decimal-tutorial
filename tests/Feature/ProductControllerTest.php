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

    public function testPriceWithVat()
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $vat = config('product.vat');

        $calculatedVatAmount = $product->price->cents * $vat / 100;
        $calculatedPriceWithVat = $product->price->cents + $calculatedVatAmount;

        $vatAmountString = number_format(
            $calculatedVatAmount / 100,
            2,
            '.',
            ''
        );

        $priceWithVatString = number_format(
            $calculatedPriceWithVat / 100,
            2,
            '.',
            ''
        );

        // Заваливается примерно в половине случаев
        $this
            ->getJson(route('products.show', $product))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->where('title', $product->title)
                    ->where('discount', (string) $product->discount)
                    ->where('price', (string) $product->price)
                    ->where('vat_amount', $vatAmountString)
                    ->where('price_with_vat', $priceWithVatString)
                    ->where('discounted_price', $product->discounted_price)
                    ->etc()
            );
    }
}
