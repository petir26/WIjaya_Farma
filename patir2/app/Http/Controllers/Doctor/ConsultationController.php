<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $query = Consultation::query()
            ->with(['user', 'messages' => fn ($messages) => $messages->latest()])
            ->withCount('messages')
            ->where('doctor_name', '!=', 'Customer Service')
            ->latest('updated_at');

        if (! $this->isGenericDoctor($request)) {
            $query->whereIn('doctor_name', $this->doctorNames($request));
        }

        $consultations = $query->get();

        return view('dokter.konsultasi', compact('consultations'));
    }

    public function show(Request $request, Consultation $consultation)
    {
        $this->authorizeDoctor($request, $consultation);
        $consultation->load(['user', 'messages']);

        return view('dokter.chat', compact('consultation'));
    }

    public function reply(Request $request, Consultation $consultation)
    {
        $this->authorizeDoctor($request, $consultation);
        $data = $request->validate([
            'message' => ['nullable', 'string', 'min:1', 'max:5000', 'required_without:attachment'],
            'attachment' => ['nullable', 'image', 'max:5120'],
        ]);

        $message = $consultation->messages()->create([
            'user_id' => $request->user()->id,
            'sender_type' => 'doctor',
            'message' => $data['message'] ?? 'Foto terlampir',
            'attachment_path' => $request->file('attachment')?->store('consultation-attachments/'.$consultation->user_id, 'local'),
        ]);

        $consultation->update(['status' => 'dibalas']);

        return response()->json(['item' => [
            'id' => $message->id,
            'sender_type' => $message->sender_type,
            'message' => $message->message,
            'attachment_url' => $message->attachment_path ? route('doctor.consultations.attachment', $message) : null,
        ]]);
    }

    public function attachment(Request $request, ConsultationMessage $message): StreamedResponse
    {
        $this->authorizeDoctor($request, $message->consultation);
        abort_unless($message->attachment_path && Storage::disk('local')->exists($message->attachment_path), 404);

        return Storage::disk('local')->response($message->attachment_path);
    }

    private function authorizeDoctor(Request $request, Consultation $consultation): void
    {
        abort_unless(
            $this->isGenericDoctor($request) || in_array($consultation->doctor_name, $this->doctorNames($request), true),
            404,
        );
    }

    /**
     * Keep the client-facing doctor names linked to their login accounts.
     * The Ojan card predates the account and uses a longer display name.
     *
     * @return array<int, string>
     */
    private function doctorNames(Request $request): array
    {
        $user = $request->user();
        $displayName = match ($user->email) {
            'ojan@gmail.com' => 'Dr. Jon D Ojan',
            'syarif@gmail.com' => 'Ir. Dr. Syarif',
            'fatir@gmail.com' => 'Dr. Fatir D H',
            default => $user->name,
        };

        return array_values(array_unique([$user->name, $displayName]));
    }

    private function isGenericDoctor(Request $request): bool
    {
        return $request->user()->role === 'dokter'
            && mb_strtolower($request->user()->name) === 'dokter';
    }
}
