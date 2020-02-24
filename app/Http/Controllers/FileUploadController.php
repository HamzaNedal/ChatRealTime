<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
class FileUploadController extends Controller
{
    function index()
    {
     return view('file_upload');
    }

    function upload(Request $request)
    {
     $rules = array(
      'file'  => 'required'
     );

     $error = Validator::make($request->all(), $rules);

     if($error->fails())
     {
      return response()->json(['errors' => $error->errors()->all()]);
     }

     $image = $request->file('file');

     $new_name = rand() . '.' . $image->getClientOriginalExtension();
     $image->move(public_path('images'), $new_name);
     if ( strtolower($image->getClientOriginalExtension())== "mp4") {
        $output = array(
            'success' => 'Image uploaded successfully',
            'image'  => '<video width="320" height="240" controls>
            <source src="/images/'.$new_name.'" type="video/mp4">
            Your browser does not support the video tag.
          </video>
          '
           );
     }else{
        $output = array(
            'success' => 'Image uploaded successfully',
            'image'  => '<img src="/images/'.$new_name.'" class="img-thumbnail" />'
           );

     }

        return response()->json($output);
    }
}
