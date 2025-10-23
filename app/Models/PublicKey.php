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
    }

    public function getPublicKey(Request $request) {
        $key = User::where('id', $request->user_id)
                ->get('public_key');

        return response()->json($key);
    }

}
