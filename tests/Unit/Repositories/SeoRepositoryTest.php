<?php

namespace Tests\Unit\Repositories;

use App\Enums\App;
use App\Enums\CacheDuration;
use App\Enums\Pages as EnumsSeo;
use App\Enums\Pages;
use App\Models\Seo;
use App\Repositories\SeoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;
    protected $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheService = $this->createMock(\App\Services\CacheService::class);
        $this->repository = new SeoRepository($this->cacheService);
    }

    public function testCreate()
    {
        $seo = Seo::factory()->make()->toArray();
        $createdSeo = $this->repository->create($seo);

        $this->assertInstanceOf(Seo::class, $createdSeo);
        $dbSeo = $this->repository->getById($createdSeo->id);
        $this->assertEquals($seo['title']['fr'], $dbSeo->title['fr']);
    }

    public function testDelete()
    {
        $seo = Seo::factory()->create();
        $isDeleted = $this->repository->delete($seo->id);
        $this->assertTrue($isDeleted);
        $this->assertSoftDeleted('seos', ['id' => $seo->id]);
    }

    public function testRestore()
    {
        $seo = Seo::factory()->create();
        $seo->delete();

        $isRestored = $this->repository->restore($seo->id);

        $this->assertTrue($isRestored);
        $this->assertDatabaseHas('seos', ['id' => $seo->id]);
    }

    public function testGetById()
    {
        $seo = Seo::factory()->create();

        $foundSeo = $this->repository->getById($seo->id);

        $this->assertInstanceOf(Seo::class, $foundSeo);
        $this->assertEquals($seo->id, $foundSeo->id);
    }

    public function testAll()
    {
        Seo::factory(1)->create();

        $seos = $this->repository->all();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $seos);
        $this->assertCount(1, $seos);
    }

    public function testGetAvvaillable()
    {
        $pages = array_map(function ($page) {
            return $page->value;
        }, EnumsSeo::cases());

        Seo::factory()->create(['key' => $pages[0]]);

        $availablePages = $this->repository->getAvvaillable();

        $this->assertIsArray($availablePages);
        $this->assertCount(count($pages) - 1, $availablePages);
    }

    public function testGetAllFromCache()
    {
        $page = Pages::CONTACT->value;
        $seo = Seo::factory()->create(['key' => $page]);

        $this->cacheService->expects($this->once())
            ->method('remember')
            ->with($page, CacheDuration::SHORT->value, $this->isType('callable'))
            ->willReturn($seo);

        $cachedSeo = $this->repository->getAllFromCache($page);

        $this->assertEquals($seo, $cachedSeo);
    }

    public function testClearCache()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("This method is not used use: clearAllCache");
        $this->repository->clearCache();
    }

    public function testPaginate()
    {
        Seo::factory(1)->create();

        $seos = $this->repository->paginate();

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $seos);
        $this->assertCount(1, $seos);
    }
}
