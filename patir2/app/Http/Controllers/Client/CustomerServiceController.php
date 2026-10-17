<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CustomerServiceMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $messages = $request->user()->customerServiceMessages()->oldest()->get();

        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['message' => ['required', 'string', 'min:1', 'max:5000']]);
        
        $hasPriorMessages = $request->user()->customerServiceMessages()->exists();
        
        // Auto-reply jika ini pesan pertama
        if (!$hasPriorMessages) {
            $data['admin_reply'] = 'Terimakasih sudah menghubungi Customer Service! Silahkan tuliskan keluhan anda.';
            $data['status'] = 'dibalas';
        }

        $message = $request->user()->customerServiceMessages()->create($data);

        return response()->json([
            'message' => 'Pesan bantuan berhasil dikirim.', 
            'item' => $message,
            'is_first' => !$hasPriorMessages,
            'auto_reply' => !$hasPriorMessages ? $data['admin_reply'] : null
        ], 201);
    }

    public function destroy(Request $request, CustomerServiceMessage $message): JsonResponse
    {
        abort_unless($message->user_id === $request->user()->id, 404);

        if ($message->status !== 'terkirim') {
            return response()->json(['message' => 'Pesan yang sudah ditangani tidak dapat dihapus.'], 422);
        }

        $message->delete();

        return response()->json(['message' => 'Pesan bantuan dihapus.']);
    }
}
