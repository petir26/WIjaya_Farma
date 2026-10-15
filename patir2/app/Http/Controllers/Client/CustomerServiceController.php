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
        return response()->json(['messages' => $request->user()->customerServiceMessages()->latest()->paginate(10)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['message' => ['required', 'string', 'min:3', 'max:5000']]);
        $message = $request->user()->customerServiceMessages()->create($data);

        return response()->json(['message' => 'Pesan bantuan berhasil dikirim.', 'item' => $message], 201);
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
