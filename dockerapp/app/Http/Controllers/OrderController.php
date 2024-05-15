<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddProductToOrderRequest;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Mail\SendMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function resetApplication()
    {
        // Order::truncate();
        // Product::truncate();
        return response()->json(204);
    }
    public function index()
    {
        $this->authorize('viewAny', Order::class);
        $orders  = Order::orderBy('created_at', 'desc')->get();
        return response()->json($orders);
    }
    public function indexPending()
    {
        $this->authorize('viewAny', Order::class);
        $orders  = Order::where('status', Order::STATUS_PENDING)->orderBy('created_at', 'desc')->get();
        return response()->json($orders);
    }
    public function indexApproved()
    {
        $this->authorize('viewApproved', Order::class);
        $orders  = Order::where('status', Order::STATUS_APPROVED)
        ->orWhere('status', Order::STATUS_STARTED)
        ->orderBy('created_at', 'desc')->get();
        return response()->json($orders);
    }
    public function indexUserOrders($id)
    {
        $this->authorize('viewUserOrders', Order::class);
        $orders  = Order::where('user_id', $id)
        ->orderBy('created_at', 'desc')->get();
        return response()->json($orders);
    }
    public function indexFinished()
    {
        $this->authorize('viewAny', Order::class);
        $orders  = Order::where('status', Order::STATUS_FINISHED)->orderBy('created_at', 'desc')->get();
        return response()->json($orders);
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
        $this->authorize('create', Order::class);
        $order = new Order;
        $order->name = $request->name;
        $order->payment_path = $request->payment_path;
        $order->save();
        $user = Auth::user();
        $order->user()->associate($user);
        $order->save();

        return response()->json($order, 201);
    }

    public function addProduct(AddProductToOrderRequest $request, $id){
        $this->authorize('update', Order::class);
        $order = Order::find($id);

        if(empty($order)){
            return response()->json([
                "message"=>"Order not found"
            ], 404);
        }

        $product = Product::find($request->product_id);

        if(empty($product)){
            return response()->json([
                "message"=>"Product not found"
            ], 404);
        }

        $quantity = $request->quantity;

        $order->products()->attach([$product->id=>["quantity"=>$quantity]]);
        $order->save();
        return response()->json($order, 200);
    }

    public function approve($id) {
        $this->authorize('approveOrReject', Order::class);

        $order = Order::find($id);

        if(empty($order)){
            return response()->json([
                "message"=>"Order not found"
            ], 404);
        }



        $order->status = Order::STATUS_APPROVED;
        $order->save();

        $user = $order->user;
        Mail::to($user->email)->send(new SendMail(
            "Tu pedido ha sido aprobado! ✅",
            "Pedido " .$order->name. " Aprobado",
            "La orden ha pasado a cocina",
             ));
        return response()->json($order, 200);
    }
    public function reject($id) {
        $this->authorize('approveOrReject', Order::class);

        $order = Order::find($id);

        if(empty($order)){
            return response()->json([
                "message"=>"Order not found"
            ], 404);
        }

        $order->status = Order::STATUS_REJECTED;
        $order->save();

        $user = $order->user;
        Mail::to($user->email)->send(new SendMail(
            "Tu pedido ha sido rechazado ❌",
            "Pedido " .$order->name. " rechazado",
            "No fue posible verificar tu pago",
             ));
        return response()->json($order, 200);
    }
    public function start($id) {
        $this->authorize('startOrFinish', Order::class);

        $order = Order::find($id);

        if(empty($order)){
            return response()->json([
                "message"=>"Order not found"
            ], 404);
        }

        $order->status = Order::STATUS_STARTED;
        $order->save();

        $user = $order->user;
        Mail::to($user->email)->send(new SendMail(
            "Tu pedido esta en preparacion! 🧑‍🍳",
            "Pedido " .$order->name. " en preparacion",
            "Pronto estará listo!",
             ));
        return response()->json($order, 200);
    }
    public function finish($id) {
        $this->authorize('startOrFinish', Order::class);

        $order = Order::find($id);

        if(empty($order)){
            return response()->json([
                "message"=>"Order not found"
            ], 404);
        }

        $order->status = Order::STATUS_FINISHED;
        $order->save();

        $user = $order->user;
        Mail::to($user->email)->send(new SendMail(
            "Tu pedido esta listo! 😋",
            "Pedido " .$order->name. " Listo",
            "Puedes pasar a recogerlo en cafeteria",
             ));
        return response()->json($order, 200);
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::find($id);

        if(empty($order)){
            return response()->json([
                "message"=>"Order not found"
            ], 404);
        }

        return response()->json($order);

    }
    public function showProducts($id)
    {
        $order = Order::find($id);

        if(empty($order)){
            return response()->json([
                "message"=>"Order not found"
            ], 404);
        }

        return response()->json($order->products->toArray());
    }

    public function showUser($id)
    {
        $order = Order::find($id);

        if(empty($order)){
            return response()->json([
                "message"=>"Order not found"
            ], 404);
        }

        return response()->json($order->user);
    }

    public function showDetails($id) {
        $order = Order::find($id);

        if(empty($order)){
            return response()->json([
                "message"=>"Order not found"
            ], 404);
        }


        $products = $order->products; // Get the related products

        $totalPrice = $products->sum(function ($product) {
            return $product->pivot->quantity * $product->price;
        });

        // ... rest of your code to prepare the response

        return response()->json([
            "id" => $order->id,
            "name" => $order->name,
            "payment_path" => $order->payment_path,
            "user_name" => $order->user->name,
            "created_at" => $order->created_at,
            "updated_at" => $order->updated_at,
            "status" => "STATUS_REJECTED", // Assuming a placeholder value
            "total" => $totalPrice,
            "products" => $products->map(function ($product) {
                return [
                    "name" => $product->name,
                    "quantity" => $product->pivot->quantity,
                    "price" => $product->price,
                ];
            })->toArray(),
        ]);
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
    public function update(UpdateOrderRequest $request, Order $order)
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
