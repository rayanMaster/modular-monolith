<?php

namespace App\Repository;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract readonly class MainRepository implements MainRepositoryInterface
{

    public function __construct(
        private Builder                  $query,
        private DatabaseManager $databaseManager

    )
    {
    }

    public function all(): Collection|array
    {
        return $this->query->get();
    }


    public function show(int $id): Model|Collection|Builder|array|null
    {
        $record = $this->query->findOrFail($id);
        return $record;
    }

    /**
     * @throws \Throwable
     */
    public function create(array $attributes) : Model|null
    {
        return $this->databaseManager->transaction(
            callback: function () use ($attributes) {
                $this->query->create($attributes);
            }, attempts: 3);
    }

    /**
     * @throws \Throwable
     */
    public function update(int $id, array $attributes, bool $passNull = false) : Model|null
    {

        $record = $this->query->findOrFail($id);
//        $filteredAttributes = array_filter($attributes, fn($attribute) => $attribute != null);
        dd($attributes);
        $record->update($attributes);

//        return $this->databaseManager->transaction(
//            callback: function () use ($attributes, $record, $passNull) {
//                $filteredAttributes = array_filter($attributes, fn($attribute) => $attribute != null);
//                $record->update($passNull ? $attributes : $filteredAttributes);
//            }, attempts: 3);
    }

    /**
     * @throws \Throwable
     */
    public function delete($id): void
    {
        $record = $this->query->findOrFail($id);
        $this->databaseManager->transaction(
            callback: function () use ($record) {
                $record->delete();
            }, attempts: 3
        );
    }
}
