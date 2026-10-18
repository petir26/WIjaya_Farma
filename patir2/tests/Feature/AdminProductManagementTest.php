<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function ($table): void {
                $table->string('role')->default('user');
            });
        }
    }

    public function test_admin_login_enters_the_admin_store_and_user_pages_redirect_back_to_it(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.test']);
        $admin->forceFill(['role' => 'admin'])->save();

        $this->post(route('login.store'), [
            'email' => 'admin@example.test',
            'password' => 'password',
        ])->assertRedirect(route('admin.toko'));

        $this->assertAuthenticatedAs($admin);
        $this->get(route('beranda'))->assertRedirect(route('admin.toko'));
        $this->get(route('toko'))->assertRedirect(route('admin.toko'));
        $this->get(route('konsultasi'))->assertRedirect(route('admin.toko'));
        $this->get(route('riwayat'))->assertRedirect(route('admin.toko'));
    }

    public function test_only_admins_can_access_product_management(): void
    {
        $this->get(route('admin.toko'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())
            ->get(route('admin.toko'))
            ->assertForbidden();
    }

    public function test_admin_can_create_edit_update_stock_and_deactivate_products(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $this->actingAs($admin);

        $this->get(route('admin.toko'))
            ->assertOk()
            ->assertSee('Kelola Toko')
            ->assertSee('Tambah Obat')
            ->assertDontSee('Konsultasi');
        $this->get(route('profil'))
            ->assertOk()
            ->assertSee('aria-label="Navigasi admin"', false)
            ->assertDontSee('<span>Beranda</span>', false)
            ->assertDontSee('<span>Riwayat</span>', false);

        $this->post(route('admin.products.store'), [
            'name' => 'Obat Uji Admin',
            'category' => 'Vitamin',
            'package' => '10 tablet',
            'price' => 12500,
            'stock' => 12,
            'description' => 'Produk untuk pengujian.',
            'content' => 'Vitamin C',
            'dose' => "Satu tablet sehari\nSesudah makan",
            'warning' => 'Simpan di tempat kering.',
            'is_active' => '1',
        ])->assertRedirect(route('admin.toko'));

        $product = Product::query()->where('name', 'Obat Uji Admin')->firstOrFail();
        $this->assertSame('obat-uji-admin', $product->slug);
        $this->assertSame(['Satu tablet sehari', 'Sesudah makan'], $product->dose);
        $this->assertTrue($product->is_active);

        $this->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertSee('Edit Obat');

        $this->patch(route('admin.products.update', $product), [
            'name' => 'Obat Uji Admin Baru',
            'category' => 'Vitamin',
            'package' => '20 tablet',
            'price' => 18000,
            'stock' => 15,
            'description' => 'Deskripsi yang telah diperbarui.',
            'content' => 'Vitamin C',
            'dose' => 'Satu tablet sehari',
            'warning' => '',
            'is_active' => '1',
        ])->assertRedirect(route('admin.toko'));
        $product->refresh();
        $this->assertSame('Obat Uji Admin Baru', $product->name);
        $this->assertSame('obat-uji-admin-baru', $product->slug);

        $this->patch(route('admin.products.stock', $product), ['stock' => 25])
            ->assertRedirect(route('admin.toko'));
        $this->assertSame(25, $product->fresh()->stock);

        $this->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.toko'));
        $this->assertFalse($product->fresh()->is_active);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Obat Uji Admin Baru']);
    }
}
