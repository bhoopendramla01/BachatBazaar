<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Shipping;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\alert;

class ShippingController extends Controller
{
     public function create()
     {
        $countries = Country::get();

        $shipping = Shipping::leftJoin('countries','countries.id','shipping_charges.country_id')->get();
        return view("admin/shipping/create", compact("countries","shipping"));
     }

     public function store(Request $request)
     {
        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->passes()) {
            $shiping = new Shipping;
            $shiping->country_id = $request->country;
            $shiping->amount = $request->amount;
            $shiping->save();

            session()->flash('success', 'Shipping Charges added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Shipping Charges added Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
     }

     public function destroy($id)
    {
        $shipping = Shipping::find($id);

        // dd($shipping->amount);

        // alert('hello');

        if (empty($shipping)) {
            session()->flash('error', 'Shipping Charges Not Found');
            return redirect('admin/shipping/create');
        }

        $shipping->delete();

        session()->flash('success', 'Shipping Charges deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Shipping Charges deleted Successfully'
        ]);
    }
}
