<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;

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
}
