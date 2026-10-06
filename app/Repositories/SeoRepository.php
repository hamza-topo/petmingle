<?php

namespace App\Repositories;

use App\Enums\App;
use App\Enums\CacheDuration;
use App\Enums\Pages as EnumsSeo;
use App\Models\Seo;
use App\Services\CacheService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use ($seo) {
            $trashed = Seo::onlyTrashed()->where('key', $seo['key'])->first();
            if ($trashed !== null) {
                $trashed->fill($seo);
                $trashed->restore();
                $trashed->refresh();
                $this->cacheService->clear($trashed->key);

                return $trashed;
            }

            $created = Seo::create($seo)->refresh();
            $this->cacheService->clear($created->key);

            return $created;
        });
    }

    /**
     * Update Seo Entity
     */
    public function update(int $seoId, array $newSeo): Seo
    {
        $seo = $this->getById($seoId);
        $seo->update($newSeo);
        $seo->refresh();
        $this->cacheService->clear($seo->key);

        return $seo;
    }

    /**
     * getById Method
     */
    public function getById(int $seoId): Seo
    {
        return Seo::findOrFail($seoId);
    }

    /**
     * Delete Seo Entity
     */
    public function delete(int $seoId): bool
    {
        $seo = $this->getById($seoId);
        $deleted = $seo->delete();
        $this->cacheService->clear($seo->key);

        return (bool) $deleted;
    }

    /**
     * Restore Trashed Seo Entity
     */
    public function restore(int $seoId): bool
    {
        $seo = Seo::withTrashed()->findOrFail($seoId);
        $restored = $seo->restore();
        $this->cacheService->clear($seo->key);

        return $restored;
    }

    /**
     * Get All Seo Entities
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
        return $this->cacheService->remember($page, CacheDuration::SHORT->value, function () use ($page) {
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
