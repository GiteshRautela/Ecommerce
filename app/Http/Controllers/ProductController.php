<?php

namespace App\Http\Controllers;

// App\Models\Product is no longer directly used here for querying
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\ProductRepositoryInterface; // Import the interface
use App\Models\Product; // Still needed for route model binding if used, or typehinting

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProductResource::collection($this->productRepository->getAll());
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
    public function store(StoreProductRequest $request)
    {
        $product = $this->productRepository->create($request->validated());
        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     * If using Route Model Binding for Product, it will be resolved before this method.
     * Otherwise, you might fetch it using $this->productRepository->findById($id);
     */
    public function show(Product $product) // Assumes Route Model Binding is resolving Product
    {
        // If not using RMB, you would do:
        // $product = $this->productRepository->findById($id);
        // if (!$product) { return response()->json(['message' => 'Product not found'], 404); }
        return new ProductResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product) // Assumes RMB
    {
        // If not using RMB for $product, you'd pass $product->id to the repository update method
        $updatedProduct = $this->productRepository->update($product->id, $request->validated());
        if (!$updatedProduct) {
            return response()->json(['message' => 'Product not found or update failed'], 404);
        }
        return new ProductResource($updatedProduct);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product) // Assumes RMB
    {
        // If not using RMB, pass $product->id
        if ($this->productRepository->delete($product->id)) {
            return response()->json(null, 204);
        }
        return response()->json(['message' => 'Product not found or delete failed'], 404);
    }
}
