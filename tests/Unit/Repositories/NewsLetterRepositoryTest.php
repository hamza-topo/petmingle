<?php

namespace Tests\Unit\Repositories;

use App\Enums\NewsLetter as EnumsNewsLetter;
use App\Models\NewsLetter;
use App\Repositories\NewsLetterRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class NewsLetterRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new NewsLetterRepository();
    }

    public function testCreate()
    {
        $newsLetter = NewsLetter::factory()->make()->toArray();
        $createdNewsLetter = $this->repository->create($newsLetter);

        $this->assertInstanceOf(NewsLetter::class, $createdNewsLetter);
        $this->assertDatabaseHas('news_letters', $newsLetter);
    }

    public function testUpdate()
    {
        $newsLetter = NewsLetter::factory()->create();
        $newNewsLetterData = ['title' => 'Updated Title'];

        $updatedNewsLetter = $this->repository->update($newsLetter->id, $newNewsLetterData);

        $this->assertInstanceOf(NewsLetter::class, $updatedNewsLetter);
        $this->assertEquals('Updated Title', $updatedNewsLetter->title);
        $this->assertDatabaseHas('news_letters', ['id' => $newsLetter->id, 'title' => 'Updated Title']);
    }

    public function testDelete()
    {
        $newsLetter = NewsLetter::factory()->create();
        $isDeleted = $this->repository->delete($newsLetter->id);
        $this->assertTrue($isDeleted);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->repository->getById($newsLetter->id);
        $this->repository->getById($newsLetter->id);
    }

    public function testRestore()
    {
        $newsLetter = NewsLetter::factory()->create();
        $newsLetter->delete();

        $isRestored = $this->repository->restore($newsLetter->id);

        $this->assertTrue($isRestored);
        $this->assertDatabaseHas('news_letters', ['id' => $newsLetter->id]);
    }

    public function testGetById()
    {
        $newsLetter = NewsLetter::factory()->create();

        $foundNewsLetter = $this->repository->getById($newsLetter->id);

        $this->assertInstanceOf(NewsLetter::class, $foundNewsLetter);
        $this->assertEquals($newsLetter->id, $foundNewsLetter->id);
    }

    public function testAll()
    {
        NewsLetter::factory(3)->create();

        $newsLetters = $this->repository->all();

        $this->assertInstanceOf(Collection::class, $newsLetters);
        $this->assertCount(3, $newsLetters);
    }

    public function testTake()
    {
        NewsLetter::factory(5)->create();

        $newsLetters = $this->repository->take();

        $this->assertInstanceOf(Collection::class, $newsLetters);
        $this->assertCount(EnumsNewsLetter::TAKE, $newsLetters);
    }

    public function testPaginate()
    {
        NewsLetter::factory(50)->create();

        $newsLetters = $this->repository->paginate();

        $this->assertInstanceOf(LengthAwarePaginator::class, $newsLetters);
        $this->assertCount(EnumsNewsLetter::PAGINATE, $newsLetters);
    }

    public function testGetByActivity()
    {
        NewsLetter::factory(1)->create(['active' => true]);
        NewsLetter::factory(1)->create(['active' => false]);
        $activeNewsLetters = $this->repository->getByActivity();

        $this->assertInstanceOf(Collection::class, $activeNewsLetters);
        $this->assertCount(1, $activeNewsLetters);
    }
}
