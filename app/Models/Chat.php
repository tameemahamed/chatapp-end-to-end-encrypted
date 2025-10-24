<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Chat extends Model
{
    //
    public static function getList() {
        $user_id = Auth::id();

        $chats = self::where('sender_id', $user_id)
                    ->orWhere('receiver_id', $user_id)
                    ->get();
        $lastPerPartner = $chats
                            ->keyBy(fn($chat) => $chat->sender_id == $user_id ? $chat->receiver_id : $chat->sender_id)
                            ->values(); 
        foreach ($lastPerPartner as $chat) {
            $partnerId = $chat->sender_id == $user_id ? $chat->receiver_id : $chat->sender_id;
            $partner_info = User::where('id', $partnerId)->first(['name', 'profile_picture_url']);
            $chat->setAttribute('partner_id', $partnerId);
            $chat->setAttribute('partner_name', $partner_info->name);
            $chat->setAttribute('partner_profile_picture_url', $partner_info->profile_picture_url);
        }
        return $lastPerPartner;
    }

}
