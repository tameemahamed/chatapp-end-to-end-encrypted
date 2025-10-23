<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicKey extends Model
{
    //
    public function updatePublicKey(Request $request) {
        $key = $request->public_key;
        $user = Auth::user();

        $user->update([
            'public_key' => $key
        ]);
    }

}
