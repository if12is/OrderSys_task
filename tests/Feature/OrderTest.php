<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    // A test method that asserts the order controller store action works as expected
    public function test_order_controller_store_action()
    {
        // Create some products and ingredients using factories
        $burger = Product::factory()->hasAttached(
            Ingredient::factory()->count(3),
            ['amount' => 150]
        )->create();

        $pizza = Product::factory()->hasAttached(
            Ingredient::factory()->count(4),
            ['amount' => 100]
        )->create();

        // Prepare the request payload
        $payload = [
            'products' => [
                ['product_id' => $burger->id, 'quantity' => 2],
                ['product_id' => $pizza->id, 'quantity' => 1],
            ]
        ];

        // Fake the mail facade
        Mail::fake();

        // Send a post request to the order controller store action with the payload
        $response = $this->postJson('/api/orders', $payload);

        // Assert the response status is 200
        $response->assertStatus(200);

        // Assert the response data contains the success status and the order data
        $response->assertJson([
            'success' => true,
            'order' => [
                'id' => 1,
                'products' => [
                    ['id' => $burger->id, 'pivot' => ['quantity' => 2]],
                    ['id' => $pizza->id, 'pivot' => ['quantity' => 1]],
                ]
            ]
        ]);

        // Assert the order was inserted into the database
        $this->assertDatabaseHas('orders', ['id' => 1]);

        // Assert the order products pivot table was updated
        $this->assertDatabaseHas('order_product', [
            'order_id' => 1,
            'product_id' => $burger->id,
            'quantity' => 2
        ]);

        $this->assertDatabaseHas('order_product', [
            'order_id' => 1,
            'product_id' => $pizza->id,
            'quantity' => 1
        ]);

        // Assert the stock of the ingredients was updated
        foreach ($burger->ingredients as $ingredient) {
            $this->assertDatabaseHas('ingredients', [
                'id' => $ingredient->id,
                'stock' => $ingredient->stock - ($ingredient->pivot->amount * 2)
            ]);
        }

        foreach ($pizza->ingredients as $ingredient) {
            $this->assertDatabaseHas('ingredients', [
                'id' => $ingredient->id,
                'stock' => $ingredient->stock - ($ingredient->pivot->amount * 1)
            ]);
        }

        // Assert an email was sent if any ingredient stock is below 50%
        foreach (Ingredient::all() as $ingredient) {
            if ($ingredient->stock <= $ingredient->threshold) {
                Mail::assertSent(IngredientAlert::class, function ($mail) use ($ingredient) {
                    return $mail->hasTo('merchant@example.com') &&
                           $mail->ingredient->is($ingredient);
                });
            } else {
                Mail::assertNotSent(IngredientAlert::class, function ($mail) use ($ingredient) {
                    return $mail->ingredient->is($ingredient);
                });
            }
        }
    }

}
