<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;

class MessagesController extends Controller
{
    public function getMessages($id)
    {
        $messages = Message::where('board_id', $id)->with('user')->get();

        return response($messages);
    }

    public function sendMessage(Request $request)
    {
        \Log::debug($request);
        $message = new Message;
        $message->fill($request->all())->save();

        return response($message);
    }
}
