<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function data(Request $request): JsonResponse
    {
        return response()->json(['client' => $request->user()]);
    }

    public function show(Request $request): View
    {
        return view('profil', ['client' => $request->user()]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
            'blood_type' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'weight_kg' => ['nullable', 'numeric', 'between:0,500'],
            'height_cm' => ['nullable', 'numeric', 'between:0,300'],
            'medical_history' => ['nullable', 'string', 'max:5000'],
        ]);

        $request->user()->update($data);

        return response()->json(['message' => 'Profil berhasil diperbarui.', 'client' => $request->user()->fresh()]);
    }
}
