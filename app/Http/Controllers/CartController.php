<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Darryldecode\Cart\Facades\CartFacade as Cart;


class CartController extends Controller
{
    /**
     * Display the cart contents.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $carts = Cart::getContent();

        return view('frontend.cart.index', compact('carts'));
    }

    /**
     * Add an item to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'productId' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['productId']);

        $item = [
            'id' => md5($product->id),
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $validated['quantity'],
            'associatedModel' => $product,
        ];

        Cart::add($item);

        Log::info('Item added to cart', ['item' => $item]);

        return response()->json([
            'status' => 200,
            'message' => 'Item successfully added to cart!',
        ]);
    }

    /**
     * Get the cart contents and total.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showCart()
    {
        $carts = Cart::getContent();
        $cartTotal = Cart::getTotal();

        Log::info('Cart contents fetched', ['carts' => $carts, 'total' => $cartTotal]);

        return response()->json([
            'status' => 200,
            'carts' => $carts,
            'cart_total' => $cartTotal,
        ]);
    }

    /**
     * Update an item's quantity in the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $cartId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $cartId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        Cart::update($cartId, [
            'quantity' => [
                'relative' => false,
                'value' => $validated['quantity'],
            ],
        ]);

        $carts = Cart::getContent();
        $cartTotal = Cart::getTotal();

        Log::info('Cart item updated', ['cart_id' => $cartId, 'quantity' => $validated['quantity']]);

        return response()->json([
            'status' => 200,
            'message' => 'Cart item updated successfully!',
            'carts' => $carts,
            'cart_total' => $cartTotal,
        ]);
    }

    /**
     * Remove an item from the cart.
     *
     * @param  string  $cartId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($cartId)
    {
        Cart::remove($cartId);

        $cartTotal = Cart::getTotal();

        Log::info('Cart item removed', ['cart_id' => $cartId]);

        return response()->json([
            'status' => 200,
            'message' => 'Cart item removed successfully!',
            'cart_total' => $cartTotal,
        ]);
    }
}
