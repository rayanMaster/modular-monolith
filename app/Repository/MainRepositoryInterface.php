<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface MainRepositoryInterface
{
    public function list(): Collection|array;

    public function show(int $id): Model|Collection|Builder|array|null;

    public function create(array $attributes) : Model|null;

    public function update(int $id, array $attributes, bool $passNull = false) : Model|null;

    public function delete($id): void;

}
