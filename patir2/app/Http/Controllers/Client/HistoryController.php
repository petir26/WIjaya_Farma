<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        return view('riwayat', [
            'orders' => $request->user()->orders()->with('items')->latest()->get(),
            'consultations' => $request->user()->consultations()->with(['messages' => fn ($query) => $query->latest()->limit(1)])->latest()->get(),
        ]);
    }
}
