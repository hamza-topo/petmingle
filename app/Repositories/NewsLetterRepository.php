<?php

namespace App\Repositories;

use App\Enums\NewsLetter as EnumsNewsLetter;
use App\Models\NewsLetter;
use Exception;
use Illuminate\Support\Collection;

class NewsLetterRepository implements RepositoryInterface
{
    public function create(array $newsLetter): NewsLetter
    {
        return NewsLetter::create($newsLetter);
    }

    public function update(int $newsLetterId, array $newsLetter): NewsLetter
    {
        $oldNewsLetter = $this->getById($newsLetterId);
        $oldNewsLetter->update($newsLetter);
        $oldNewsLetter->refresh();

        return $oldNewsLetter;
    }

    public function delete(int $newsLetterId): bool
    {
        return NewsLetter::destroy($newsLetterId);
    }

    public function restore(int $newsLetterId): bool
    {
        return NewsLetter::withTrashed()->find($newsLetterId)->restore();
    }

    public function getById(int $newsLetterId): NewsLetter
    {
        return NewsLetter::findOrFail($newsLetterId);
    }

    public function all()
    {
        return NewsLetter::all();
    }
    /**
     * Take News to display
     *
     * @param [type] $take
     * @return void
     */
    public function take(int $take = EnumsNewsLetter::TAKE)
    {
        return NewsLetter::take($take)->get();
    }

    public function paginate(int|null $page = EnumsNewsLetter::PAGINATE)
    {
        return NewsLetter::paginate($page);
    }


    public function getByActivity(bool $isActive = true)
    {
        return NewsLetter::where('active', $isActive)->get();
    }

    public function getByTypes(array $types, bool $isActive = true): Collection
    {
        return NewsLetter::whereIn('type', $types)->where('active', $isActive)->get();
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
        throw new Exception('Not defined yet');
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function clearCache(): bool
    {
        throw  new Exception('Not defined yet');
    }
}
