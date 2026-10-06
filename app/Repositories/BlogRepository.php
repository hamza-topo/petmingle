<?php

namespace App\Repositories;

use App\Enums\App as EnumsLike;
use App\Enums\Pages;
use App\Models\Blog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BlogRepository
{
    public function create(array $like): Blog
    {
        return Blog::create($like);
    }

    public function update(int $likeId, array $newModel): Blog
    {
        $like = $this->getById($likeId);
        $like->update($newModel);
        $like->refresh();

        return $like;
    }

    /**
     * getById
     *
     * @param  mixed  $likeId
     */
    public function getById(int $likeId): ?Blog
    {
        return Blog::find($likeId);
    }

    public function delete(int $likeId): bool
    {
        return Blog::destroy($likeId);
    }

    public function getBySlug(string $slug, string $local): Blog
    {
        return Blog::where("slug->{$local}", $slug)->firstOrFail();
    }

    public function random(): Collection
    {
        return Blog::inRandomOrder()->limit(5)->get();
    }

    public function restore(int $likeId): bool
    {
        return Blog::withTrashed()->findOrFail($likeId)->restore();
    }

    public function all(): Collection
    {
        return Blog::all();
    }

    public function Blogs(?int $petId): LengthAwarePaginator
    {
        return Blog::with(['to', 'from'])->where('from', $petId)->paginate(EnumsLike::PAGINATE);
    }

    public function paginate(bool $exlude = false): LengthAwarePaginator
    {
        $locale = app()->getLocale(); // Get the current locale
        $slugs = collect(Pages::cases())->pluck('value')->map(function ($slug) {
            return strtolower(slugify($slug));
        })->toArray();

        $query = Blog::orderBy('id', 'DESC')
            ->where('active', true)
            ->whereNull('publish_it_at');

        if ($exlude) {
            $query->whereNotIn("slug->$locale", $slugs);
        }

        return $query->paginate(EnumsLike::PAGINATE);
    }

    /**
     * get Scheduled blogs
     */
    public function getScheduled(): Collection
    {
        return Blog::where('active', false)->where('publish_it_at', '!=', null)->get();
    }

    public function getDueForPublication(string $date): Collection
    {
        $query = Blog::where('active', false)
            ->where('publish_it_at', '!=', null)
            ->where('publish_it_at', '<=', $date);

        return $query->get();
    }

    public function publishBulk(array $ids = []): int
    {
        return Blog::whereIn('id', $ids)->update([
            'active' => true,
            'publish_it_at' => null,
        ]);
    }

    public function take(?int $limit = EnumsLike::PAGINATE): Collection
    {
        return Blog::orderBy('created_at')->limit($limit)->get()->filter(function ($row) {
            return ! empty($row->slug['en']) && $row->slug['en'] != 'about';
        });
    }
}
