<?php

namespace App\Repositories;

use App\Enums\Like as EnumsLike;
use App\Models\Blog;
use App\Models\Like;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use function PHPUnit\Framework\isTrue;

class BlogRepository implements RepositoryInterface
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
     * @param  mixed $likeId
     * @return Blog
     */
    public function getById(int $likeId): Blog
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

    public function Blogs(int $petId): LengthAwarePaginator
    {
        return Blog::with(['to', 'from'])->where('from', $petId)->paginate(EnumsLike::PAGINATE);
    }

    public function paginate()
    {
        return Blog::orderByg('id', 'DESC')->paginate(EnumsLike::PAGINATE);
    }

    //TODO::create another enum class for blog
    public function take(?int $limit = EnumsLike::PAGINATE)
    {
        return Blog::orderBy('created_at')->limit($limit)->get()->filter(function($row){
            return !empty($row->slug['en']) && $row->slug['en'] != 'about';
        });
    }



    /**
     * getAllFromCache method
     *
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @param ?string $key
     * @return mixed
     */
    public function getAllFromCache(?string $key = ''): mixed
    {
        return [];
    }

    public function clearCache(): bool
    {
        return true;
    }
}
