<?php

namespace Tests\Feature\Database;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RelationalIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_foreign_key_rejects_orphan_location(): void
    {
        $this->expectException(QueryException::class);

        DB::table('locations')->insert([
            'user_id' => 999999,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_hard_deleted_user_cascades_owned_location(): void
    {
        $user = User::factory()->create();

        DB::table('locations')->insert([
            'user_id' => $user->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('locations', [
            'user_id' => $user->id,
        ]);

        DB::table('users')
            ->where('id', $user->id)
            ->delete();

        $this->assertDatabaseMissing('locations', [
            'user_id' => $user->id,
        ]);
    }

    public function test_species_cannot_be_hard_deleted_while_referenced_by_race(): void
    {
        $speciesId = DB::table('species')->insertGetId([
            'name' => 'Dog',
            'description' => 'Test species',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('races')->insert([
            'species_id' => $speciesId,
            'name' => 'Mixed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            DB::table('species')
                ->where('id', $speciesId)
                ->delete();

            $this->fail(
                'Expected species deletion to be rejected while referenced by a race.'
            );
        } catch (QueryException) {
            $this->assertDatabaseHas('species', [
                'id' => $speciesId,
            ]);
        }
    }
}
