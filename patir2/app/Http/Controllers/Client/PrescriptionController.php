<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrescriptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['prescriptions' => $request->user()->prescriptions()->with('product')->whereNull('order_id')->latest()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->where('requires_prescription', true)],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);
        $product = Product::query()->whereKey($data['product_id'])->where('requires_prescription', true)->firstOrFail();
        $file = $request->file('file');
        $path = $file->store('prescriptions/' . $request->user()->id, 'local');
        $prescription = $request->user()->prescriptions()->create([
            'product_id' => $product->id,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
        ]);

        return response()->json(['message' => 'Resep berhasil diunggah.', 'prescription' => $prescription->load('product')], 201);
    }

    public function download(Request $request, Prescription $prescription): StreamedResponse
    {
        abort_unless($prescription->user_id === $request->user()->id, 404);

        return Storage::disk('local')->download($prescription->file_path, $prescription->original_name);
    }

    public function destroy(Request $request, Prescription $prescription): JsonResponse
    {
        abort_unless($prescription->user_id === $request->user()->id, 404);
        abort_if($prescription->order_id !== null, 422, 'Resep yang telah dikaitkan dengan pesanan tidak dapat dihapus.');
        Storage::disk('local')->delete($prescription->file_path);
        $prescription->delete();

        return response()->json(['message' => 'Resep dihapus.']);
    }
}
