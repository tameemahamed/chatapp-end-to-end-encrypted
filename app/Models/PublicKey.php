<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicKey extends Model
{
    //
    public function updatePublicKey(Request $request) {
        $key = $request->public_key;
        $user_id = Auth::id();

        User::where('id', $user_id)
        ->update([
            'public_key' => $key
        ]);

        // delete all of the existing chats of the user
        Chat::where('sender_id', $user_id)
                ->orWhere('receiver_id', $user_id)
                ->delete();
    }

    public function getPublicKey(Request $request) {
        $key = User::where('id', $request->user_id)
                ->firstOrFail('public_key');

        return response()->json($key);
    }

    public function checkPublicKey(Request $request) {
        $key = $request->public_key;
        $user_id = Auth::id();

        // make it true or false and return
        $found = User::where('id', $user_id)
                    ->where('public_key', $key)
                    ->exists();
        
        return response()->json($found);
    }
}
