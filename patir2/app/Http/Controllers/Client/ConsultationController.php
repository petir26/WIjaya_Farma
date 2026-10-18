<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConsultationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['consultations' => $request->user()->consultations()->withCount('messages')->latest()->paginate(10)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'doctor_name' => ['required', Rule::in(['Dr. Jon D Ojan', 'Ir. Dr. Syarif', 'Dr. Fatir D H', 'Customer Service'])],
            'complaint' => ['nullable', 'string', 'min:1', 'max:5000', 'required_without:attachment'],
            'attachment' => ['nullable', 'image', 'max:5120'],
        ]);
        $complaint = $data['complaint'] ?? 'Foto terlampir';

        if ($data['doctor_name'] === 'Customer Service') {
            $data['status'] = 'dibalas';
        }

        $consultation = $request->user()->consultations()->create([
            'doctor_name' => $data['doctor_name'],
            'complaint' => $complaint,
            'status' => $data['status'] ?? 'menunggu_dokter',
        ]);
        $consultation->messages()->create([
            'user_id' => $request->user()->id,
            'sender_type' => 'client',
            'message' => $complaint,
            'attachment_path' => $request->file('attachment')?->store('consultation-attachments/' . $request->user()->id, 'local'),
        ]);

        if ($data['doctor_name'] === 'Customer Service') {
            $consultation->messages()->create([
                'user_id' => null,
                'sender_type' => 'doctor',
                'message' => 'Terimakasih sudah menghubungi Customer Service! Silahkan tuliskan keluhan anda.',
            ]);
        }

        return response()->json(['message' => 'Konsultasi berhasil dibuat.', 'consultation' => $consultation->load('messages')], 201);
    }

    public function show(Request $request, Consultation $consultation): JsonResponse
    {
        abort_unless($consultation->user_id === $request->user()->id, 404);

        return response()->json(['consultation' => $consultation->load('messages')]);
    }

    public function attachment(Request $request, ConsultationMessage $message): StreamedResponse
    {
        abort_unless($message->consultation->user_id === $request->user()->id, 404);
        abort_unless($message->attachment_path && Storage::disk('local')->exists($message->attachment_path), 404);

        return Storage::disk('local')->response($message->attachment_path);
    }

    public function sendMessage(Request $request, Consultation $consultation): JsonResponse
    {
        abort_unless($consultation->user_id === $request->user()->id, 404);
        $data = $request->validate([
            'message' => ['nullable', 'string', 'min:1', 'max:5000', 'required_without:attachment'],
            'attachment' => ['nullable', 'image', 'max:5120'],
        ]);
        $message = $consultation->messages()->create([
            'user_id' => $request->user()->id,
            'sender_type' => 'client',
            'message' => $data['message'] ?? 'Foto terlampir',
            'attachment_path' => $request->file('attachment')?->store('consultation-attachments/' . $request->user()->id, 'local'),
        ]);

        return response()->json(['message' => 'Pesan konsultasi tersimpan.', 'item' => $message], 201);
    }

    public function destroy(Request $request, Consultation $consultation): JsonResponse
    {
        abort_unless($consultation->user_id === $request->user()->id, 404);

        if ($consultation->status !== 'menunggu_dokter') {
            return response()->json(['message' => 'Konsultasi yang sudah ditangani tidak dapat dihapus.'], 422);
        }

        $consultation->delete();

        return response()->json(['message' => 'Konsultasi dibatalkan.']);
    }
}
