<?php

namespace App\Http\Controllers\API;

use App\Chat;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;

class ChatController extends Controller
{
    public function index($user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return $this->sendResponse('error', 'User Tidak Ada', null, 404);
        }
        // dd($user->role);
        
        $from = User::select('users.id', 'users.name', 'users.email')->leftJoin('chats', 'users.id' , '=', 'chats.from')->where('chats.to', $user_id)->where('users.id', '!=', $user_id)->groupBy('users.id', 'users.name', 'users.email')->with(['userdetail' => function($query) {
            $query->select('user_id', 'avatar'); 
        }, 'shop' => function($query) {
            $query->select('user_id', 'shop_name', 'image');
        }])->get()->toArray();

        $to = User::select('users.id', 'users.name', 'users.email')->leftJoin('chats', 'users.id' , '=', 'chats.to')->where('chats.from', $user_id)->where('users.id', '!=', $user_id)->groupBy('users.id', 'users.name', 'users.email')->with(['userdetail' => function($query) {
            $query->select('user_id', 'avatar');
        }, 'shop' => function($query) {
            $query->select('user_id', 'shop_name', 'image');
        }])->get()->toArray();

        // $chats = User::select('users.id', 'users.name', 'users.email', DB::raw('COUNT(is_read) AS unread'))
        //     ->leftJoin('chats', 'users.id' , '=', 'chats.from')
        //     ->where('is_read', 0)
        //     ->where('chats.to', $user_id)
        //     ->where('users.id', '!=', $user_id)
        //     ->orWhere(function($query) use ($user_id) {
        //         $query->where('chats.from', $user_id)->where('chats.to', $user_id);
        //     })
        //     ->groupBy('users.id', 'users.name', 'users.email')
        //     ->with(['userdetail:user_id,avatar', 'shop:user_id,shop_name,image'])
        //     ->get();

        $unread = Chat::select(DB::raw('COUNT(is_read) as unread'))->where('to', $user_id)->where('is_read', 0)->first();

        $chats = array_unique(array_merge($from, $to), SORT_REGULAR);

        if (!$chats) {
            return $this->sendResponse('error', 'Chat Kosong', null, 404);
        }

        return $this->sendResponse('success', 'Chat Berhasil Diambil', compact('chats', 'unread'), 200);
    }

    public function getMessage(Request $request, $user_id)
    {
        $from = $request->from;

        if ($user_id == $from) {
            return $this->sendResponse('error', 'User sama', null, 404);
        }

        // when click readed
        Chat::where(['from' => $from, 'to' => $user_id])->update(['is_read' => 1]);
        
        $chats = Chat::where(function ($query) use ($user_id, $from) {
            $query->where('from', $from)->where('to', $user_id);
        })->orWhere(function ($query) use ($user_id, $from) {
            $query->where('from', $user_id)->where('to', $from);
        })->get();
        // dd(date('d M y, h:i a', strtotime($chats[3]->created_at)));
        
        if ($chats->isEmpty()) {
            return $this->sendResponse('error', 'Chat Kosong', null, 404);
        }

        return $this->sendResponse('success', 'Chat Berhasil Diambil', $chats, 200);
    }

    public function sendMessage(Request $request, $user_id)
    {
        if ($user_id == $request->to) {
            return $this->sendResponse('error', 'User sama', null, 404);
        }

        $validator = Validator::make($request->all(), [
            'to' => 'required|integer',
            'chat' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response($validator->errors());
        }

        $chat = new Chat;
        $chat->from = $user_id;
        $chat->to = $request->to;
        $chat->chat = $request->chat;

        try {
            $chat->save();

            // pusher
            $options = [
                'cluster' => 'ap1',
                'useTLS'=> true
            ];

            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );

            // $data = ['from' => $user_id, 'to' => $request->to]; // sending from and to user id when enter
            $pusher->trigger('my-channel', 'my-event', $chat);

            return $this->sendResponse('success', 'Chat Dikirim', $chat, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse('error', 'Chat Gagal Dikirim', $th->getMessage(), 500);
        }
    }

    public function destroyMessage($id)
    {
        $chat = Chat::find($id);

        if (!$chat) {
            return $this->sendResponse('error', 'Data Tidak Ada', null, 404);
        }

        try {
            $chat->delete();

            return $this->sendResponse('success', 'Chat Dihapus', null, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse('error', 'Chat Gagal Dihapus', null, 404);
        }
    }

    public function destroyUserMessage(Request $request)
    {
        if ($request->from == $request->to) {
            return $this->sendResponse('error', 'User sama', null, 404);
        }

        $from = Chat::where('from', $request->from)->where('to', $request->to)->get();
        $to = Chat::where('from', $request->to)->where('to', $request->from)->get();

        if ($from->isEmpty() && $to->isEmpty()) {
            return $this->sendResponse('error', 'Data Tidak Ada', null, 404);
        }

        $from = Chat::where('from', $request->from)->where('to', $request->to);
        $to = Chat::where('from', $request->to)->where('to', $request->from);
                
        try {
            $from->delete();
            $to->delete();

            return $this->sendResponse('success', 'Chat Dihapus', null, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse('error', 'Chat Gagal Dihapus', null, 404);
        }
    }
}
