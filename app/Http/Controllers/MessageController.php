<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required'],
        ]);

        $message = Message::create($data);

        return response()->json($message);
    }
}
