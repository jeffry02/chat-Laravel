<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Events\MessageCreated;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(string $receiver,string $type): JsonResponse
    {
        $messages = [];

        if ($type == 'private'){
            $messages = Message::query()
                ->with('receiver', 'sender')
                ->where(function (Builder $builder) use ($receiver) {
                    $builder->where('receiver_id', $receiver)
                        ->where('sender_id', Auth::id());
                })
                ->orWhere(function (Builder $builder) use ($receiver) {
                    $builder->where('receiver_id', Auth::id())
                        ->where('sender_id', $receiver);
                })
                ->orderBy('created_at')
                ->get();
        }else{
            $messages = Message::query()
                ->with('sender')
                ->where(function (Builder  $builder) use ($receiver) {
                    $builder->where('channel_id', $receiver );
                })
//                ->where('channel_id', $receiver)
                ->orderBy('created_at')
                ->get();
        }
//        $messages = Message::query()
//            ->with('receiver', 'sender')
//            ->where(function (Builder $builder) use ($receiver) {
//                $builder->where('receiver_id', $receiver)
//                    ->where('sender_id', Auth::id());
//            })
//            ->orWhere(function (Builder $builder) use ($receiver) {
//                $builder->where('receiver_id', Auth::id())
//                    ->where('sender_id', $receiver);
//            })
//            ->orderBy('created_at')
//            ->get();

        return response()->json($messages);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required'],
            'room_id' => ['required'],
            'channel' => ['required']
        ]);

        $message = [];

        if ($data['channel'] == 'private'){
            $message = Message::create([
                'receiver_id' => $data['room_id'],
                'sender_id' => Auth::id(),
                'channel_id' => 1,
                'message' => $data['message'],
            ]);
        }else{
            $message = Message::create([
                'receiver_id' => 1,
                'sender_id' => Auth::id(),
                'channel_id' => $data['room_id'],
                'message' => $data['message'],
            ]);
        }

        broadcast(new MessageCreated($message))->toOthers();

        return response()->json($message);
    }
}
