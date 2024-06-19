<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModelClass of Model
 */
interface MainRepositoryInterface
{
    /**
     * @return Collection<int,TModelClass>|array<TModelClass>
     */
    public function list(): Collection|array;

    /**
     * @param int $id
     * @return Model|Collection<int,TModelClass>|Builder<TModelClass>|array<TModelClass>|null
     */
    public function show(int $id): Model|Collection|Builder|array|null;

    /**
     * @param array<string,mixed> $attributes
     * @return Model|null
     */
    public function create(array $attributes): Model|null;

    /**
     * @param int $id
     * @param array<string,mixed>$attributes
     * @param bool $passNull
     * @return Model|null
     */
    public function update(int $id, array $attributes, bool $passNull = false): ?Model;

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;
}
