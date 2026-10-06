<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComponentValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_input_is_validated_before_persistence(): void
    {
        $admin = User::withoutEvents(fn () => User::factory()->create(['is_admin' => true]));
        $this->actingAs($admin)->post('/admin/components', ['name' => 'unknown', 'title' => 'invalid'])
            ->assertSessionHasErrors(['name', 'title']);
        $this->assertDatabaseCount('components', 0);

        $this->post('/admin/components', ['name' => 'c-plan', 'title' => ['en' => 'Plans']])
            ->assertRedirect(route('admin.components.index'));
        $this->assertDatabaseHas('components', ['name' => 'c-plan']);
    }
}
