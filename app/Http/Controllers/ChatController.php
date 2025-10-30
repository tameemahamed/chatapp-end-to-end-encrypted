<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function insertMessage(Request $request) {
        $id = Str::uuid();
        $sender_id = Auth::id();
        $receiver_id = $request->receiver_id;
        $sender_en_msg = $request->sender_en_msg;
        $receiver_en_msg = $request->receiver_en_msg;
        $created_at = now();
        
        Chat::insert([
            'id' => $id,
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'sender_en_msg' => $sender_en_msg,
            'receiver_en_msg' => $receiver_en_msg,
            'created_at' => $created_at
        ]);
    }

    public function chatList() {
        if(Auth::user()) {
            $list = Chat::getList();
            return response()->json($list);
        }
    }

    public function individualMessages(Request $request) {
        if(Auth::user()) {
            $messages = Chat::getMessages($request->partner_id);
            return response()->json($messages);
        }
    }

    public function searchedUser(Request $request) {
        $name = $request->name;
        if(Auth::user() && $name) {
            $users = User::where('name', 'LIKE', '%'.$name.'%')
                        ->limit(3)
                        ->get(['id', 'name']);
            return response()->json($users);
        }
        return [];
    }
}
