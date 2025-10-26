<?php

namespace App\Models;

use DB;
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

    public static function getMessages($partner_id) {
        $user_id = Auth::id();
    
        $sent = self::where('sender_id', $user_id)
            ->where('receiver_id', $partner_id)
            ->select('sender_en_msg as message', DB::raw("'sent' as type"), 'created_at', 'updated_at', 'delivered_at', 'read_at');
    
        $received = self::where('sender_id', $partner_id)
            ->where('receiver_id', $user_id)
            ->select('receiver_en_msg as message', DB::raw("'received' as type"), 'created_at', 'updated_at', 'delivered_at', 'read_at');
    
        if($user_id==$partner_id) return $sent->orderBy('created_at', 'asc')->get();
        return $sent->union($received)->orderBy('created_at', 'asc')->get();
    }
}
