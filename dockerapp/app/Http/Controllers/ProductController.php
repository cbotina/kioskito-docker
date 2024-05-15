<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Product::class);
        $products  = Product::all();
        return response()->json($products);
    }
    public function indexAvailable()
    {
        $this->authorize('viewAvailable', Product::class);
        $products  = Product::where('available', true)->get();
        return response()->json($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);
        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->available = $request->available;
        $product->image_path = $request->image_path;
        $product->price = $request->price;
        $product->save();

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('view', Product::class);
        $product = Product::find($id);

        if(!empty($product)){
            return response()->json($product);
        } else {
            return response()->json([
                "message"=>"Product not found"
            ], 404);
        }

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
    public function update(UpdateProductRequest $request, $id)
    {
        $this->authorize('update', Product::class);
        $product = Product::find($id);

        if(empty($product)){
            return response()->json([
                "message"=>"Product not found"
            ], 404);
        }

        $product->name = $request->has('name') ? $request->input('name') : $product->name;
        $product->description = $request->has('description') ? $request->input('description') : $product->description;
        $product->available = $request->has('available') ? $request->input('available') : $product->available;
        $product->price = $request->has('price') ? $request->input('price') : $product->price;
        $product->image_path = $request->has('image_path') ? $request->input('image_path') : $product->image_path;


        $product->save();


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $this->authorize('delete', Product::class);
        $product = Product::find($id);

        if(!empty($product)){
            $product->delete();
        }
        return response()->json([], 204);
    }
}
