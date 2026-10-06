<?php

namespace App\Repositories;

use App\Models\Block;
use Illuminate\Support\Collection;

class BlockRepository
{
    public function create(array $block): Block
    {
        return Block::create($block);
    }

    // TODO:pagination and caching
    public function blocks(int $userId): Collection
    {
        return Block::where('from', $userId)->get();
    }
}
