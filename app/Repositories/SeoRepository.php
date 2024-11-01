<?php

namespace App\Repositories;

use App\Enums\App;
use App\Enums\CacheDuration;
use App\Enums\Pages as EnumsSeo;
use App\Models\Seo;
use App\Services\CacheService;

/**
 * Seo Repository class
 */
class SeoRepository implements RepositoryInterface
{
    /**
     * SeoRepository constructor
     *
     * @param CacheService $cacheService
     */
    public function __construct(protected CacheService $cacheService) {}

    /**
     * Store New Seo Entity
     *
     * @param array $seo
     * @return Seo
     */
    public function create(array $seo): Seo
    {   
        return Seo::create($seo);
    }

    /**
     * Update Seo Entity
     *
     * @param integer $seoId
     * @param array $newSeo
     * @return mixed
     */
    public function update(int $seoId, array $newSeo): mixed
    {
        $seo = $this->getById($seoId);
        $seo->update($newSeo);
        $seo->refresh();

        return $seo;
    }

    /**
     * getById Method
     *
     * @param integer $seoId
     * @return void
     */
    public function getById(int $seoId)
    {
        return Seo::findOrFail($seoId);
    }

    /**
     * Delete Seo Entity
     *
     * @param integer $seoId
     * @return boolean
     */
    public function delete(int $seoId): bool
    {
        return Seo::destroy($seoId);
    }

    /**
     * Restore Trashed Seo Entity
     *
     * @param integer $seoId
     * @return boolean
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
    public function all()
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
     *
     * @param array $keys
     * @return mixed
     */
    public function notCreatedYet(array $keys): mixed
    {
        throw new \Exception('Method deprecated. Use [getAvvaillable()] method instead.');
    }

    /**
     * Get Seo that Are available similare
     * Its based on @see App\Enums\Pages::CASES()
     *
     * @return void
     */
    public function getAvvaillable()
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
     * @param ?string $page
     * @return mixed
     */
    public function getAllFromCache(?string $page = ''): mixed
    {
        return $this->cacheService->remember($page, CacheDuration::SHORT->value, function ($page) {
            return Seo::where('key', $page)->firstOrFail();
        });
    }


    public function clearCache(): bool
    {
        throw new \Exception("This method is not used use: clearAllCache", 1);
    }

    /**
     * Pagination method
     *
     * @param int|null $paginate
     * @return void
     */
    public function paginate(int|null $paginate = App::PAGINATE)
    {
        return Seo::OrderBy('id', App::ORDER)->paginate($paginate);
    }
}
