<?php

namespace App\Http\Controllers;

use App\Message;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('chats');
    }

    public function fetchmessage(){
        return Message::with('user')->get();
    }

    public function sendmessage(Request $request){
        $user= Auth()->user();
        $message = $user->messages()->create([
            'message' => $request->message
        ]);

        broadcast(new MessageSent($user,$message))->toOthers();
        return response()->json(['status' => 'Message Sent!']);
    }
}
