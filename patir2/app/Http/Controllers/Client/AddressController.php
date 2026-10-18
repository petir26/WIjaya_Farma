<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\SavedAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function page(Request $request): View
    {
        return view('checkout.alamat', [
            'addresses' => $request->user()->savedAddresses()->orderByDesc('is_default')->latest()->get(),
            'mapsApiKey' => config('services.google_maps.key'),
            'client' => $request->user(),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->savedAddresses()->orderByDesc('is_default')->latest()->get();

        return response()->json([
            'addresses' => $addresses,
            'selected_address_id' => $addresses->firstWhere('is_default', true)?->id,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateAddress($request);
        $user = $request->user();

        $address = DB::transaction(function () use ($data, $user) {
            $makeDefault = ($data['is_default'] ?? false) || ! $user->savedAddresses()->exists();
            if ($makeDefault) $user->savedAddresses()->update(['is_default' => false]);

            $address = $user->savedAddresses()->create([...$data, 'is_default' => $makeDefault]);
            if ($makeDefault) $user->update(['address' => $address->address, 'phone' => $address->phone]);

            return $address;
        });

        return response()->json(['message' => 'Alamat berhasil disimpan.', 'address' => $address], 201);
    }

    public function update(Request $request, SavedAddress $address): JsonResponse
    {
        $this->ensureOwned($request, $address);
        $data = $this->validateAddress($request);
        $user = $request->user();

        DB::transaction(function () use ($address, $data, $user) {
            $makeDefault = ($data['is_default'] ?? false) || $address->is_default;
            if ($makeDefault) $user->savedAddresses()->where('id', '<>', $address->id)->update(['is_default' => false]);
            $address->update([...$data, 'is_default' => $makeDefault]);
            if ($makeDefault) $user->update(['address' => $address->address, 'phone' => $address->phone]);
        });

        return response()->json(['message' => 'Alamat berhasil diperbarui.', 'address' => $address->fresh()], 200);
    }

    public function select(Request $request, SavedAddress $address): JsonResponse
    {
        $this->ensureOwned($request, $address);
        $user = $request->user();

        DB::transaction(function () use ($user, $address) {
            $user->savedAddresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
            $user->update(['address' => $address->address, 'phone' => $address->phone]);
        });

        return response()->json(['message' => 'Alamat pengiriman dipilih.', 'address' => $address->fresh()]);
    }

    public function destroy(Request $request, SavedAddress $address): JsonResponse
    {
        $this->ensureOwned($request, $address);
        $user = $request->user();
        $wasDefault = $address->is_default;

        DB::transaction(function () use ($address, $user, $wasDefault) {
            $address->delete();
            if (! $wasDefault) return;

            $replacement = $user->savedAddresses()->latest()->first();
            if ($replacement) {
                $replacement->update(['is_default' => true]);
                $user->update(['address' => $replacement->address, 'phone' => $replacement->phone]);
            } else {
                $user->update(['address' => null]);
            }
        });

        return response()->json(['message' => 'Alamat berhasil dihapus.']);
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:40'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:2000'],
            'place_id' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'notes' => ['nullable', 'string', 'max:500'],
            'is_default' => ['sometimes', 'boolean'],
        ]);
    }

    private function ensureOwned(Request $request, SavedAddress $address): void
    {
        abort_unless($address->user_id === $request->user()->id, 404);
    }
}
