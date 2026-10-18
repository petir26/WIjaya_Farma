<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('dokter.profil', ['doctor' => $request->user()]);
    }

    public function update(Request $request): JsonResponse
    {
        $doctor = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($doctor->id)],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $doctor->update($data);

        return response()->json(['message' => 'Profil dokter berhasil diperbarui.', 'doctor' => $doctor->fresh()]);
    }
}
