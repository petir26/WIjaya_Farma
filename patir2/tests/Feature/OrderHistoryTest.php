<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_navigating_away_from_order_history_does_not_cancel_pending_order(): void
    {
        $user = User::factory()->create();
        $order = $user->orders()->create([
            'order_number' => 'WF-20261018-ABC123',
            'total_amount' => 25000,
            'status' => 'menunggu_pembayaran',
            'fulfillment_method' => 'delivery',
            'shipping_address' => 'Jl. Contoh No. 1',
            'payment_method' => 'QRIS',
            'payment_status' => 'belum_dibayar',
        ]);

        $this->actingAs($user)
            ->get(route('riwayat'))
            ->assertOk()
            ->assertSee('class="purchase-history-actions"', false)
            ->assertSee('Bayar')
            ->assertSee('Batalkan Pesanan');

        $this->get(route('beranda'))->assertOk();
        $this->get(route('checkout'))->assertOk();
        $this->get(route('checkout.confirmation'))->assertOk();

        $this->assertSame('menunggu_pembayaran', $order->fresh()->status);
    }
}
