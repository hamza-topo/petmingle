<?php

namespace App\Repositories;

use App\Enums\NewsLetter as EnumsNewsLetter;
use App\Models\NewsLetter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NewsLetterRepository
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

    public function all(): Collection
    {
        return NewsLetter::all();
    }
    /**
     * Take News to display
     *
     * @param [type] $take
     * @return void
     */
    public function take(int $take = EnumsNewsLetter::TAKE): Collection
    {
        return NewsLetter::take($take)->get();
    }

    public function paginate(?int $page = EnumsNewsLetter::PAGINATE): LengthAwarePaginator
    {
        return NewsLetter::paginate($page);
    }


    public function getByActivity(bool $isActive = true): Collection
    {
        return NewsLetter::where('active', $isActive)->get();
    }

    public function getByTypes(array $types, bool $isActive = true): Collection
    {
        return NewsLetter::whereIn('type', $types)->where('active', $isActive)->get();
    }
}
