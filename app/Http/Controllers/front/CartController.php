<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Country;
use App\Models\Order;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $product = Product::with('product_images')->find($request->id);

        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.',
                session()->flash("error", 'Product not found.')
            ]);
        }

        if (Cart::count() > 0) {
            $cartContent = Cart::content();
            $productAlreadyExist = false;

            foreach ($cartContent as $item) {
                if ($item->id == $product->id) {
                    $productAlreadyExist = true;
                }
            }

            if ($productAlreadyExist == false) {
                Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
                $status = true;
                $message = 'Product added in Cart.';
            } else {
                $message = 'Product Already added in Cart.';
                return response()->json([
                    'status' => false,
                    'message' => $message,
                    session()->flash("error", $message)
                ]);
            }
        } else {
            Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
            $status = true;
            $message = 'Product added in Cart.';
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            session()->flash("success", $message)
        ]);
    }

    public function cart()
    {
        $cartContent = Cart::content();
        return view("front/cart", compact("cartContent"));
    }

    public function updateCart(Request $request)
    {
        $rowId = $request->rowId;
        $qty = $request->qty;
        // dd('hello');
        $itemInfo = Cart::get($rowId);
        $product = Product::find($itemInfo->id);

        if ($product->track_qty == 'Yes') {
            if ($qty <= $product->qty) {
                Cart::update($rowId, $qty);
                $message = 'Cart updated successfully.';
                $status = true;
                session()->flash("success", $message);
            } else {
                $message = 'Requested Quantity not available in stock.';
                $status = false;
                session()->flash("error", $message);
            }
        } else {
            Cart::update($rowId, $qty);
            $message = 'Cart updated successfully.';
            $status = true;
            session()->flash("success", $message);
        }

        return response()->json([
            "status" => $status,
            'data' => $itemInfo,
            "message" => $message
        ]);
    }

    public function deleteItem(Request $request)
    {
        $rowId = $request->rowId;

        $itemInfo = Cart::get($rowId);

        if ($itemInfo == null) {
            $errorMessage = 'Item not found in Cart.';
            return response()->json([
                "status" => false,
                "message" => $errorMessage,
                session()->flash("error", $errorMessage)
            ]);
        } else {
            Cart::remove($rowId);
            $message = "Item removed from cart Successfully.";
            return response()->json([
                "status" => true,
                "message" => $message,
                session()->flash("success", $message)
            ]);
        }
    }

    public function checkout()
    {
        if (Cart::count() == 0) {
            return redirect()->route("front/cart");
        }

        if (Auth::check() == false) {
            if (!session()->has('url.intended')) {
                session(["url.intended" => url()->current()]);
            }
            return redirect()->route("account/login");
        }

        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();
        session()->forget('url.intended');

        $countries = Country::orderBy('name','ASC')->get();
        return view("front/account/checkout", compact("countries","customerAddress"));
    }

    public function processCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required' ,
            'email'=> 'required|email',
            'country'=> 'required',
            'address' => 'required',
            'city'=> 'required',
            'state'=> 'required',
            'zip'=> 'required',
            'phone'=> 'required'
        ]);

        if ($validator->passes()) {
            $user = Auth::user();

            CustomerAddress::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'first_name'=> $request->first_name,
                    'last_name'=> $request->last_name,
                    'email'=> $request->email,
                    'country_id'=> $request->country,
                    'address'=> $request->address,
                    'city'=> $request->city,
                    'state'=> $request->state,
                    'zip'=> $request->zip,
                    'phone'=> $request->phone,
                    'apartment'=> $request->apartment,
                ]);

                if($request->payment_method == 'cod'){
                    $shipping = 0;
                    $discount = 0;
                    $subTotal = Cart::subtotal(2,'.','');
                    $grandTotal = $subTotal + $shipping;

                    $order = new Order;
                    $order->subtotal = $subTotal;
                    $order->shipping = $shipping;
                    $order->discount = $discount;
                    $order->grand_total = $grandTotal;

                    $order->user_id = $user->id;
                    $order->first_name = $request->first_name;
                    $order->last_name = $request->last_name;
                    $order->email = $request->email;
                    $order->country_id = $request->country;
                    $order->address = $request->address;
                    $order->city = $request->city;
                    $order->state = $request->state;
                    $order->zip = $request->zip;
                    $order->phone = $request->phone;
                    $order->apartment = $request->apartment;
                    $order->notes = $request->notes;

                    $order->save();

                    foreach(Cart::content() as $item)
                    {
                        $orderItem = new OrderItem;
                        $orderItem->product_id = $item->id;
                        $orderItem->order_id = $order->id;
                        $orderItem->name = $item->name;
                        $orderItem->qty = $item->qty;
                        $orderItem->price = $item->price;
                        $orderItem->total = $item->price*$item->qty;

                        $orderItem->save();
                    }

                    session()->flash('success','You have successfully placed your order.');
                    return response()->json([
                        'status' => true,
                        'message'=> 'Order saved successfully.'
                    ]);
                }
            
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function thankYou()
    {
        return view('front/account/thankYou');
    }
}
