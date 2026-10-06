<?php

namespace Tests\Feature\Admin;

use App\Models\Seo;
use App\Models\Species;
use App\Models\User;
use App\Repositories\SeoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_creation_form_posts_translated_titles_and_persists_them(): void
    {
        $this->signInAsAdmin();
        $form = $this->get('/admin/seo/create')->assertOk();
        foreach (['en', 'fr', 'es'] as $locale) {
            $form->assertSee('name="title['.$locale.']"', false);
        }

        $payload = $this->payload();
        $this->post('/admin/seo', $payload)->assertRedirect(route('admin.seo.index'));
        $seo = Seo::where('key', 'HOME')->firstOrFail();

        $this->assertEquals($payload['title'], $seo->title);
        $this->assertEquals($payload['meta'], $seo->meta);
        $this->get('/admin/seo')->assertOk()->assertSee('Accueil PetMingle');
    }

    public function test_invalid_title_shape_and_duplicate_pages_are_rejected(): void
    {
        $this->signInAsAdmin();
        $payload = $this->payload();
        $payload['title'] = 'flattened title';
        $this->post('/admin/seo', $payload)->assertSessionHasErrors('title');
        $this->assertDatabaseCount('seos', 0);

        Seo::create($this->payload());
        $this->post('/admin/seo', $this->payload())->assertSessionHasErrors('key');
        $this->assertDatabaseCount('seos', 1);
    }

    public function test_delete_form_targets_seo_and_soft_deletes_without_deleting_species(): void
    {
        $this->signInAsAdmin();
        $seo = Seo::create($this->payload());
        $species = Species::unguarded(fn () => Species::create(['id' => $seo->id, 'name' => 'Dog']));
        $repository = app(SeoRepository::class);
        $repository->getAllFromCache('HOME');

        $this->get('/admin/seo')->assertOk()
            ->assertSee('action="'.route('admin.seo.destroy', $seo->id).'"', false)
            ->assertDontSee('action="'.route('admin.species.destroy', $species->id).'"', false);

        $this->delete('/admin/seo/'.$seo->id)->assertRedirect(route('admin.seo.index'));
        $this->assertSoftDeleted('seos', ['id' => $seo->id]);
        $this->assertNotSoftDeleted('species', ['id' => $species->id]);
        $this->assertNull($repository->getByKey('HOME'));
        $this->assertFalse(Cache::has('HOME'));
        $this->delete('/admin/seo/'.$seo->id)->assertNotFound();
    }

    public function test_deleted_page_can_be_created_again_without_violating_the_unique_key(): void
    {
        $this->signInAsAdmin();
        $seo = Seo::create($this->payload());
        $this->delete('/admin/seo/'.$seo->id)->assertRedirect();
        $payload = $this->payload();
        $payload['title']['fr'] = 'Nouvel accueil';

        $this->post('/admin/seo', $payload)->assertRedirect(route('admin.seo.index'));
        $this->assertDatabaseCount('seos', 1);
        $this->assertNotSoftDeleted('seos', ['id' => $seo->id]);
        $this->assertSame('Nouvel accueil', $seo->fresh()->title['fr']);
    }

    public function test_update_preserves_the_page_key_and_refreshes_cached_metadata(): void
    {
        $this->signInAsAdmin();
        $seo = Seo::create($this->payload());
        $repository = app(SeoRepository::class);
        $this->assertSame('Accueil PetMingle', $repository->getAllFromCache('HOME')->title['fr']);
        $payload = $this->payload();
        $payload['key'] = 'ABOUT';
        $payload['title']['fr'] = 'Titre modifie';

        $this->put('/admin/seo/'.$seo->id, $payload)->assertRedirect(route('admin.seo.index'));
        $this->assertSame('HOME', $seo->fresh()->key);
        $this->assertSame('Titre modifie', $repository->getAllFromCache('HOME')->title['fr']);
        $this->get('/admin/seo/'.$seo->id.'/edit')->assertOk()->assertSee('Titre modifie');
        $this->put('/admin/seo/999999', $payload)->assertNotFound();
    }

    public function test_seo_mutations_require_an_admin(): void
    {
        $seo = Seo::create($this->payload());
        $this->delete('/admin/seo/'.$seo->id)->assertUnauthorized();
        $user = User::withoutEvents(fn () => User::factory()->create(['is_admin' => false]));
        $this->actingAs($user);
        $this->post('/admin/seo', $this->payload())->assertForbidden();
        $this->put('/admin/seo/'.$seo->id, $this->payload())->assertForbidden();
        $this->delete('/admin/seo/'.$seo->id)->assertForbidden();
        $this->assertNotSoftDeleted('seos', ['id' => $seo->id]);
    }

    private function signInAsAdmin(): void
    {
        $admin = User::withoutEvents(fn () => User::factory()->create(['is_admin' => true]));
        $this->actingAs($admin);
    }

    private function payload(): array
    {
        return [
            'key' => 'HOME',
            'title' => ['en' => 'PetMingle Home', 'fr' => 'Accueil PetMingle', 'es' => 'Inicio PetMingle'],
            'meta' => ['description' => ['en' => 'Meet pets', 'fr' => 'Rencontrer des animaux', 'es' => 'Conocer mascotas']],
        ];
    }
}
