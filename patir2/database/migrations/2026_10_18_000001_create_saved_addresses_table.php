<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label', 40)->default('Rumah');
            $table->string('recipient_name', 120);
            $table->string('phone', 30);
            $table->text('address');
            $table->string('place_id', 255)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('notes', 500)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'is_default']);
        });

        DB::table('users')
            ->whereNotNull('address')
            ->where('address', '<>', '')
            ->orderBy('id')
            ->get(['id', 'name', 'phone', 'address'])
            ->each(function ($user): void {
                DB::table('saved_addresses')->insert([
                    'user_id' => $user->id,
                    'label' => 'Rumah',
                    'recipient_name' => $user->name,
                    'phone' => $user->phone ?? '',
                    'address' => $user->address,
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_addresses');
    }
};
