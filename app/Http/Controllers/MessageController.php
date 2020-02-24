<?php

namespace App\Http\Controllers;

use App\Events\MessageDelivered;
use App\Message;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use File;
class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {

        return view('messages.index');
    }
    public function chatUser($id)
    {
        $receiver = User::findOrFail($id);
        $messages =  DB::table('messages')
        ->where(['sender_id'=>auth()->user()->id,'receiver_id'=> $id])
        ->orWhere(function($query) use ($id)
        {
            $query->Where(['sender_id'=>$id,'receiver_id'=> auth()->user()->id]);
        })->get();
        return view('messages.index',compact('receiver','messages'));
    }
    public function store()
    {

        if (request()->body == null && request()->image == 'undefined') {
            return $this->responceState(true);
        }

        $message = Message::create([
            'sender_id'=>auth()->user()->id,
            'receiver_id'=>request()->receiver_id,
            'body'=>request()->body,
            'image'=> session('imageName')
        ]);
        broadcast(new MessageDelivered($message))->toOthers();
        session(['imageName'=>NULL]);
        return $this->responceState(false,$message->id, $message->image);
    }

    function upload(Request $request)
    {

     $rules = [

        'image' => 'required|image|mimes:jpeg,png,jpg',
     ];
    // $validator = Validator::make(request()->all());
     $error = Validator::make($request->all(), $rules);

     if($error->fails())
     {
      return response()->json(['message' => $error->errors()->all()]);
     }

    //  $image = $request->file('file');

     $new_name = uniqid().time().'.'.request()->image->getClientOriginalExtension();
     request()->image->move(public_path().'/images/', $new_name);

     session(['imageName'=>$new_name]);

     return json_encode(['message' => 'success']);
    }

    public function destroy()
    {
        $message = Message::find(request()->id);
        if ($message) {
            broadcast(new MessageDelivered($message,1))->toOthers();
            if(File::delete("images/".$message->image)) {
                File::delete("images/".$message->image);
            }
            $messageDel = $message->delete(request()->id);
            if ($messageDel) {
               return $this->responceState();
            }else{
                return $this->responceState(true);
            }
        }

        return $this->responceState(true);

    }


    public function responceState($error = false,$id=null,$image=null)
    {
        return response(json_encode([
            'status'=>200,
            'error'=>$error,
            'message_id'=>$id,
            'image_name'=>$image
        ]));
    }
}
