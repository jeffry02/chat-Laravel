<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $data = User::query()
            ->where('id','!=', 1)
            ->orderBy('created_at')
            ->get();
        return response()->json($data);
    }
}
