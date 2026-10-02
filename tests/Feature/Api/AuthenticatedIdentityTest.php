<?php

namespace Tests\Feature\Api;

use App\Models\Pet;
use App\Models\Race;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AuthenticatedIdentityTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::withoutEvents(fn () => User::factory()->create());
    }

    private function identity(?string $token = null, string $query = ''): TestResponse
    {
        // Each request must authenticate afresh, as a separate PHP HTTP request would.
        $this->app['auth']->forgetGuards();

        return $this->getJson('/api/v.0/me' . $query, $token === null ? [] : [
            'Authorization' => 'Bearer ' . $token,
        ]);
    }

    private function pet(User $user): Pet
    {
        $species = Species::create(['name' => 'Dog']);
        $race = Race::create(['species_id' => $species->id, 'name' => 'Mixed']);

        return Pet::create([
            'user_id' => $user->id, 'species_id' => $species->id, 'race_id' => $race->id,
            'name' => 'Nala', 'age' => 3, 'sexe' => 1, 'color' => 'gold', 'images' => [], 'about' => 'Private profile text',
        ]);
    }

    public function test_valid_bearer_token_returns_only_the_authenticated_account_and_its_pet(): void
    {
        $this->user(); // Keep user and pet sequences different to catch ID confusion.
        $user = $this->user();
        $pet = $this->pet($user);
        $token = $user->createToken('identity-test')->plainTextToken;

        $response = $this->identity($token)->assertOk()->assertExactJson([
            'success' => true,
            'message' => 'Authenticated identity.',
            'data' => [
                'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
                'pet' => ['id' => $pet->id, 'user_id' => $user->id, 'name' => 'Nala'],
            ],
        ]);
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $response->assertDontSee($token)->assertDontSee($user->password);
    }

    public function test_account_without_a_pet_receives_null_and_not_another_accounts_pet(): void
    {
        $user = $this->user();
        $this->pet($this->user());
        $this->identity($user->createToken('identity-test')->plainTextToken)
            ->assertOk()->assertJsonPath('data.user.id', $user->id)->assertJsonPath('data.pet', null);
    }

    public function test_missing_token_is_rejected(): void
    {
        $this->identity()->assertUnauthorized()->assertExactJson(['success' => false, 'message' => 'Unauthenticated.']);
    }

    public function test_invalid_token_is_rejected(): void
    {
        $user = $this->user();
        $token = $user->createToken('identity-test');
        $this->identity($token->accessToken->id . '|incorrect-secret')->assertUnauthorized();
    }

    public function test_revoked_token_is_rejected(): void
    {
        $token = $this->user()->createToken('identity-test');
        $token->accessToken->delete();
        $this->identity($token->plainTextToken)->assertUnauthorized();
    }

    public function test_query_parameters_cannot_select_another_users_identity(): void
    {
        $first = $this->user();
        $second = $this->user();
        foreach ([[$first, $second], [$second, $first]] as [$actor, $other]) {
            $this->identity($actor->createToken('identity-test')->plainTextToken,
                '?id=' . $other->id . '&user_id=' . $other->id . '&include=tokens,pet.owner')
                ->assertOk()->assertJsonPath('data.user.id', $actor->id)
                ->assertJsonPath('data.user.email', $actor->email)
                ->assertJsonMissingPath('data.user.tokens')->assertDontSee($other->email);
        }
    }

    public function test_sign_out_revokes_only_the_presented_token_and_denies_its_reuse(): void
    {
        $user = $this->user();
        $active = $user->createToken('active-device');
        $other = $user->createToken('other-device');
        $this->identity($active->plainTextToken)->assertOk();
        $this->app['auth']->forgetGuards();
        $this->postJson('/api/v.0/sign-out', [], ['Authorization' => 'Bearer ' . $active->plainTextToken])->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $active->accessToken->id]);
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $other->accessToken->id]);
        $this->identity($active->plainTextToken)->assertUnauthorized();
        $this->identity($other->plainTextToken)->assertOk();
    }

    public function test_unauthenticated_sign_out_is_rejected(): void
    {
        $this->postJson('/api/v.0/sign-out')->assertUnauthorized();
    }
}
