<?php

namespace App\Repositories;

use App\Enums\App;
use App\Enums\CacheDuration;
use App\Enums\Pages as EnumsSeo;
use App\Models\Seo;
use App\Services\CacheService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Seo Repository class
 */
class SeoRepository
{
    /**
     * SeoRepository constructor
     */
    public function __construct(protected CacheService $cacheService) {}

    /**
     * Store New Seo Entity
     */
    public function create(array $seo): Seo
    {
        return Seo::create($seo);
    }

    /**
     * Update Seo Entity
     */
    public function update(int $seoId, array $newSeo): Seo
    {
        $seo = $this->getById($seoId);
        $seo->update($newSeo);
        $seo->refresh();

        return $seo;
    }

    /**
     * getById Method
     */
    public function getById(int $seoId): ?Seo
    {
        return Seo::findOrFail($seoId);
    }

    /**
     * Delete Seo Entity
     */
    public function delete(int $seoId): bool
    {
        return Seo::destroy($seoId);
    }

    /**
     * Restore Trashed Seo Entity
     */
    public function restore(int $seoId): bool
    {
        return Seo::withTrashed()->find($seoId)->restore();
    }

    /**
     * Get All Seo Entities
     *
     * @return void
     */
    public function all(): Collection
    {
        return Seo::all();
    }

    public function getByKey(string $key): ?Seo
    {
        return Seo::where('key', $key)->first();
    }

    /**
     * Get Seo that not created yet
     * Its based on @see App\Enums\Pages::CASES()
     */
    public function notCreatedYet(array $keys): mixed
    {
        throw new \Exception('Method deprecated. Use [getAvvaillable()] method instead.');
    }

    /**
     * Get Seo that Are available similare
     * Its based on @see App\Enums\Pages::CASES()
     */
    public function getAvvaillable(): array
    {
        $pages = array_map(function ($page) {
            return $page->value;
        }, EnumsSeo::cases());

        $avpages = $this->all()->pluck('key')->toArray();

        return array_diff($pages, $avpages);
    }

    /**
     * getAllFromCache method
     *
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     */
    public function getAllFromCache(?string $page = ''): Seo
    {
        return $this->cacheService->remember($page, CacheDuration::SHORT->value, function ($page) {
            return Seo::where('key', $page)->firstOrFail();
        });
    }

    /**
     * Pagination method
     */
    public function paginate(?int $paginate = App::PAGINATE): LengthAwarePaginator
    {
        return Seo::OrderBy('id', App::ORDER)->paginate($paginate);
    }
}
