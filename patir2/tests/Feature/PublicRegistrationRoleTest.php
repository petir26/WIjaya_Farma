<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PublicRegistrationRoleTest extends TestCase
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

    public function test_public_registration_always_assigns_the_user_role(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test Customer',
            'email' => 'customer@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('beranda'));
        $user = User::query()->where('email', 'customer@example.test')->firstOrFail();

        $this->assertSame('user', $user->role);
        $this->assertAuthenticatedAs($user);
    }
}
