<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_homepage_displays_the_home_design(): void
    {
        $this->get('/')
            ->assertRedirect(route('beranda'));

        $this->get(route('beranda'))
            ->assertOk()
            ->assertSee('<h1>Beranda</h1>', false)
            ->assertSee('Kategori Obat Populer')
            ->assertSee('Artikel Kesehatan')
            ->assertSee('href="https://keslan.kemkes.go.id/view_artikel/2682/berbagai-manfaat-buah-buahan"', false)
            ->assertSee('target="_blank" rel="noopener noreferrer"', false)
            ->assertSee(route('toko'));
    }

    public function test_store_displays_products_and_category_menu(): void
    {
        $this->get(route('toko'))
            ->assertOk()
            ->assertSee('<h1>Toko</h1>', false)
            ->assertSee('Vitamin B12')
            ->assertSee('Lodia 2 mg')
            ->assertSee('Pilih kategori obat')
            ->assertSee(route('beranda'));
    }

    public function test_store_can_open_with_a_selected_category(): void
    {
        $this->get(route('toko', ['kategori' => 'vitamin']))
            ->assertOk()
            ->assertSee('value="vitamin"', false);
    }
}
