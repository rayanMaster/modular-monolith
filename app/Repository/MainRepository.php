<?php

namespace App\Repository;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use Throwable;

/**
 * @template TModelClass of Model
 */
abstract readonly class MainRepository implements MainRepositoryInterface
{


    /**
     * @param Builder<TModelClass>|null $query
     * @param DatabaseManager $databaseManager
     */
    public function __construct(
        private ?Builder $query,
        private DatabaseManager $databaseManager
    ) {
    }

    /**
     * @return Collection<int, TModelClass>|array<TModelClass>
     */
    public function list(): Collection|array
    {
        if ($this->query === null) {
            return [];
        }

        return $this->query->get();
    }

    /**
     * @param int $id
     * @return Model|Collection<int,TModelClass>|Builder<TModelClass>|array<TModelClass>|null
     */
    public function show(int $id): Model|Collection|Builder|array|null
    {
        return $this->query?->findOrFail($id);
    }

    /**
     * @param array<string,mixed>$attributes
     * @return Model|null
     * @throws Throwable
     */
    public function create(array $attributes): Model|null
    {
        return $this->databaseManager->transaction(
            callback: function () use ($attributes) {
                return $this->query?->create($attributes);
            }, attempts: 3);
    }

    /**
     * @param int $id
     * @param array<string,mixed> $attributes
     * @param bool $passNull
     * @return Model|null
     * @throws Throwable
     */
    public function update(int $id, array $attributes, bool $passNull = false): Model|null
    {
        if ($this->query === null) {
            throw new RuntimeException('Query builder is not set.');
        }
        $record = $this->query->findOrFail($id);

        return $this->databaseManager->transaction(
            callback: function () use ($attributes, $record, $passNull) {
                $filteredAttributes = array_filter($attributes, fn ($attribute) => $attribute != null);
                $record->update($passNull ? $attributes : $filteredAttributes);
                $record->refresh();
                return $record;
            }, attempts: 3);
    }

    /**
     * @param int $id
     * @return void
     * @throws Throwable
     */
    public function delete($id): void
    {
        if ($this->query === null) {
            throw new RuntimeException('Query builder is not set.');
        }

        $record = $this->query->findOrFail($id);

        $this->databaseManager->transaction(
            callback: function () use ($record) {
                return $record->delete();
            }, attempts: 3
        );
    }
}
