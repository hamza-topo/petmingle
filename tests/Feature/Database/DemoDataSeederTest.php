<?php

namespace Tests\Feature\Database;

use App\Models\Conversation;
use App\Models\Like;
use App\Models\Location;
use App\Models\MatchTable;
use App\Models\Message;
use App\Models\Pet;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_data_is_coherent_and_can_be_seeded_again_without_overwriting_data(): void
    {
        Storage::fake('public');
        Mail::fake();
        $realUser = User::withoutEvents(fn () => User::factory()->create());
        $this->seed(DemoDataSeeder::class);

        $this->assertSame(37, User::count());
        $this->assertSame(36, Pet::count());
        $this->assertSame(36, Location::count());
        $this->assertSame(27, Like::count());
        $this->assertSame(18, MatchTable::count());
        $this->assertSame(9, Conversation::count());
        $this->assertSame(27, Message::count());
        $this->assertSame(9, Message::where('is_seen', false)->count());
        Storage::disk('public')->assertExists('pets/demo/dog.jpg');
        foreach (Pet::with('race')->get() as $pet) {
            $this->assertSame((int) $pet->getAttribute('species_id'), (int) $pet->race->getAttribute('species_id'));
        }

        $demo = User::where('email', 'demo.marrakech@petmingle.test')->firstOrFail();
        $this->assertTrue(Hash::check('PetmingleDemo!2026', (string) $demo->getAttribute('password')));
        $demo->update(['name' => 'Mon compte modifié']);
        $this->seed(DemoDataSeeder::class);

        $this->assertSame(37, User::count());
        $this->assertSame(36, Pet::count());
        $this->assertSame(27, Like::count());
        $this->assertSame(18, MatchTable::count());
        $this->assertSame(9, Conversation::count());
        $this->assertSame(27, Message::count());
        $this->assertSame('Mon compte modifié', $demo->fresh()->getAttribute('name'));
        $this->assertNotNull($realUser->fresh());
        Mail::assertNothingSent();
        Mail::assertNothingQueued();
    }

    public function test_demo_data_is_rejected_outside_local_and_testing(): void
    {
        $this->app->instance('env', 'production');
        try {
            $this->seed(DemoDataSeeder::class);
            $this->fail('Production seeding must be rejected.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('local and testing', $exception->getMessage());
        }
        $this->assertSame(0, User::count());
    }
}
