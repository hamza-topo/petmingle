<?php

namespace Tests\Feature\Infrastructure;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BladeAssetLoadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_use_published_assets_without_a_mix_manifest(): void
    {
        $admin = User::withoutEvents(fn () => User::factory()->create(['is_admin' => true]));
        $response = $this->actingAs($admin)->get('/admin/seo')->assertOk();
        $response->assertSee('vendor/adminlte/dist/css/adminlte.min.css', false)
            ->assertSee('vendor/adminlte/dist/js/adminlte.min.js', false)
            ->assertDontSee('/js/app.js', false)
            ->assertDontSee('/css/app.css', false);
    }

    public function test_public_auth_page_uses_its_static_bootstrap_and_site_assets(): void
    {
        $this->get('/user/login')->assertOk()
            ->assertSee('assets/css/bootstrap-5.3.0.min.css', false)
            ->assertSee('assets/js/main.js', false)
            ->assertDontSee('/js/app.js', false)
            ->assertDontSee('/css/app.css', false);
    }
}
