<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Mail\IngredientAlert;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function stored(Request $request)
    {
        // Validate the request payload
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        // Create a new order
        $order = Order::create($request->all());

        // Loop through the products in the request
        foreach ($request->products as $product) {
            // Check that the product has a valid quantity value
            if (!isset($product['quantity']) || $product['quantity'] < 1) {
                continue; // Skip this product if it doesn't have a valid quantity
            }

            // Find the product by id
            $product = Product::find($product['product_id']);

            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found'], 404);
            }

            // Attach the product to the order with the quantity
            $order->products()->attach($product->id, ['quantity' => $product['quantity']]);

            // Loop through the ingredients of the product
            foreach ($product->ingredients as $ingredient) {
                // Calculate the amount of ingredient consumed by the product quantity
                $amount = $ingredient->pivot->amount * $product['quantity'];

                // Update the stock of the ingredient by subtracting the amount
                $ingredient->update(['stock' => $ingredient->stock - $amount]);

                // Check if the stock of the ingredient is below 50%
                if ($ingredient->stock <= $ingredient->threshold) {
                    // Send an email to alert the merchant
                    Mail::to('merchant@example.com')->send(new IngredientAlert($ingredient));
                }
            }
        }

        // Return a success response with the order
        return response()->json(['success' => true, 'order' => $order]);
    }

    // public function store(Request $request)
    // {
    //     $order = Order::create([
    //         'customer_name' => $request->input('customer_name'),
    //         'customer_email' => $request->input('customer_email'),
    //     ]);

    //     foreach ($request->input('products') as $productData) {
    //         $product = Product::find($productData['product_id']);

    //         // Create the order product
    //         $orderProduct = new OrderProduct([
    //             'product_id' => $product->id,
    //             'quantity' => $productData['quantity'],
    //             'price' => $product->price,
    //         ]);

    //         // Add the order product to the order
    //         $order->products()->save($orderProduct);

    //         // Update the stock of the ingredients
    //         foreach ($product->ingredients as $ingredient) {
    //             $usedQuantity = $ingredient->pivot->quantity * $productData['quantity'];
    //             $ingredient->quantity -= $usedQuantity;
    //             $ingredient->save();

    //             // Send email alert if necessary
    //             if ($ingredient->quantity <= ($ingredient->original_quantity / 2) && !$ingredient->alert_sent) {
    //                 $this->sendIngredientAlertEmail($ingredient);
    //                 $ingredient->alert_sent = true;
    //                 $ingredient->save();
    //             }
    //         }
    //     }

    //     return response()->json(['message' => 'Order created successfully'], 201);
    // }

    // private function sendIngredientAlertEmail(Ingredient $ingredient)
    // {
    //     // Code to send email alert goes here
    // }

    // public function store_order(Request $request)
    // {
    //     // Get the products array from the request payload
    //     $products = $request->input('products');

    //     // Create a new order with the products
    //     $order = new Order();
    //     $order->products()->attach($products);

    //     // Save the order to the database
    //     if ($order->save()) {
    //         // Update the stock of each ingredient in the order
    //         foreach ($order->products as $product) {
    //             foreach ($product->ingredients as $ingredient) {
    //                 // Subtract the ingredient amount from the stock
    //                 $ingredient->stock -= $ingredient->pivot->amount * $product->pivot->quantity;

    //                 // Save the ingredient to the database
    //                 $ingredient->save();

    //                 // Check if the ingredient stock is below 50%
    //                 if ($ingredient->stock <= $ingredient->threshold) {
    //                     // Send an email alert to the merchant
    //                     Mail::to('merchant@example.com')->send(new IngredientAlert($ingredient));
    //                 }
    //             }
    //         }

    //         // Return a success response with the order details
    //         return response()->json(['status' => 'success', 'order' => $order]);
    //     } else {
    //         // Return an error response with the validation errors
    //         return response()->json(['status' => 'error', 'errors' => $order->errors()]);
    //     }
    // }


    public function create(Request $request)
    {
        // Get the order details from the request payload
        $orderDetails = $request->json()->all();

        // Create a new Order instance using Eloquent
        $order = new Order();
        $order->customer_name = $orderDetails['customer_name'];

        // Save the order to the database
        $order->save();

        // Update the stock of the Ingredients
        foreach ($orderDetails['products'] as $product) {
            // Find the product by ID
            $productModel = Product::findOrFail($product['id']);

            // Loop through the product's ingredients and reduce their stock levels
            foreach ($productModel->ingredients as $ingredient) {
                $quantity = $ingredient->pivot->quantity * $product['quantity'];
                $ingredient->decrement('stock', $quantity);

                // Check if the stock level has fallen below 50%
                if ($ingredient->stock < $ingredient->alert_level && !$ingredient->alert_sent) {
                    // Send an email to alert the merchant
                    Mail::to('merchant@example.com')->send(new IngredientAlert($ingredient));

                    $ingredient->alert_sent = true;
                    $ingredient->save();
                }
            }
        }
    }
}
