<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Import DB facade
use App\Events\OrderPlaced; // Add this line
use App\Http\Requests\StoreOrderRequest; // Add this

class OrderController extends Controller
{
    /**
     * Display a listing of the resource for the authenticated user.
     */
    public function index()
    {
        $orders = Auth::user()->orders()->with('products')->get();
        return OrderResource::collection($orders);
    }

    /**
     * Display a listing of all orders for admin.
     */
    public function adminIndex()
    {
        $orders = Order::with('user', 'products')->get();
        return OrderResource::collection($orders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        // Validation is now handled by StoreOrderRequest
        $validatedData = $request->validated();

        return DB::transaction(function () use ($validatedData) {
            $totalAmount = 0;
            foreach ($validatedData['products'] as $productData) {
                $product = Product::findOrFail($productData['id']);
                $totalAmount += $product->price * $productData['quantity'];
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            foreach ($validatedData['products'] as $productData) {
                $order->products()->attach($productData['id'], ['quantity' => $productData['quantity']]);
            }
            
            event(new OrderPlaced($order));

            return new OrderResource($order->load('products'));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        if (Auth::id() !== $order->user_id && !(Auth::check() && Auth::user()->role === 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return new OrderResource($order->load('user', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
