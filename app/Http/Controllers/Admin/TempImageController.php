<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Image;

class TempImageController extends Controller
{
    public function create(Request $request)
    {
        // dd($request);
        $image = $request->image;
         if(!empty($image))
         {
            $ext = $image->getClientOriginalExtension();
            $newName = time().'.'.$ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path().'/tempImage',$newName);

            //Generate Thumbnail
            $sourcePath = public_path().'/tempImage/'.$newName;
            $destPath = public_path().'/tempImage/thumb/'.$newName;

            // dd($sourcePath);
            $image = Image::make($sourcePath);
            $image->fit(300,275);
            $image->save($destPath);

            return response()->json([
                'status' => 'success',
                'image_id' => $tempImage->id,
                'ImagePath' => asset('/tempImage/thumb'.$newName),
                'message' => 'Image uploaded successfully'
            ]);
         }
    }
}
