<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface RepositoryInterface
{


    /**
     * create method
     * 
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @param  mixed $model
     * @return Collection
     */
    public function create(array $model): Collection;

    /**
     * update
     * 
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @param  int $modelId
     * @param  array $newModel
     * @return Collection
     */
    public function update(int $modelId, array $newModel): Collection;

    /**
     * delete method
     * 
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @param  int $modelId
     * @return bool
     */
    public function delete(int $modelId): bool;

    /**
     * getById method
     *
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @param  mixed $modelId
     * @return Collection
     */
    public function getById(int $modelId): Collection;

    /**
     * restore method
     *
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @param  int $modelId
     * @return bool
     */
    public function restore(int $modelId): bool;

    /**
     * all method
     *
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @return Collection
     */
    public function all(): Collection;

    /**
     * paginate method
     *
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @param int $number of element to retrieve per page
     */
    // public function paginate(int $number); //TODO::add type hint 

    /**
     * getAllFromCache method
     *
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @return mixed
     */
    public function getAllFromCache(): mixed;

    /**
     * clearCache method
     * 
     * @author Topo <hamzaaitsidisaid.11@gmail.com>
     * @return bool
     */
    public function clearCache(): bool;
}
