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
            'orders' => $request->user()->orders()->with('items.product')->latest()->get(),
            'consultations' => $request->user()->consultations()->with(['messages' => fn ($query) => $query->latest()->limit(1)])->latest()->get(),
        ]);
    }

    public function showOrder(Request $request, int $order): View
    {
        $order = $request->user()->orders()
            ->with(['items.product', 'shipment', 'prescriptions.product'])
            ->findOrFail($order);

        return view('detail-riwayat', compact('order'));
    }
}
