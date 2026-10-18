<?php

namespace Tests\Feature;

use App\Models\ConsultationMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConsultationAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_and_customer_service_chat_pages_offer_photo_uploads(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('konsultasi.chat', 'syarif'))
            ->assertOk()
            ->assertSee('id="chat-attachment"', false)
            ->assertSee('id="chat-camera"', false);

        $this->get(route('customer-service.chat'))
            ->assertOk()
            ->assertSee('id="chat-attachment"', false)
            ->assertSee('id="chat-camera"', false);
    }

    public function test_client_can_start_doctor_and_customer_service_chats_with_a_photo_only(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        foreach (['Dr. Jon D Ojan', 'Customer Service'] as $doctorName) {
            $response = $this->actingAs($user)->post(route('client.consultations.store'), [
                'doctor_name' => $doctorName,
                'attachment' => UploadedFile::fake()->create('photo.jpg', 1, 'image/jpeg'),
            ], ['Accept' => 'application/json']);

            $response->assertCreated()
                ->assertJsonPath('consultation.doctor_name', $doctorName)
                ->assertJsonPath('consultation.messages.0.message', 'Foto terlampir');

            $attachment = $response->json('consultation.messages.0');
            $message = ConsultationMessage::query()->findOrFail($attachment['id']);
            Storage::disk('local')->assertExists($message->attachment_path);
            $this->get(parse_url($attachment['attachment_url'], PHP_URL_PATH))->assertOk();
        }
    }

    public function test_client_can_send_a_photo_with_a_message_in_an_existing_consultation(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $consultation = $user->consultations()->create([
            'doctor_name' => 'Ir. Dr. Syarif',
            'complaint' => 'Saya mengalami keluhan.',
        ]);

        $response = $this->actingAs($user)->post(route('client.consultations.messages.store', $consultation), [
            'message' => 'Ini foto keluhannya.',
            'attachment' => UploadedFile::fake()->create('symptom.jpg', 1, 'image/jpeg'),
        ], ['Accept' => 'application/json']);

        $response->assertCreated()
            ->assertJsonPath('item.message', 'Ini foto keluhannya.');
        $this->assertNotEmpty($response->json('item.attachment_url'));
    }
}
