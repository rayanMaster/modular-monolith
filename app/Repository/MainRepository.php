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
 * @implements MainRepositoryInterface<TModelClass>
 */
abstract readonly class MainRepository implements MainRepositoryInterface
{
    /**
     * @param  Builder<TModelClass>|null  $query
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
     * @return Model|Collection<int,TModelClass>|Builder<TModelClass>|array<TModelClass>|null
     */
    public function show(int $id): Model|Collection|Builder|array|null
    {
        return $this->query?->findOrFail($id);
    }

    /**
     * @param  array<string,mixed>  $attributes
     *
     * @throws Throwable
     */
    public function create(array $attributes): ?Model
    {
        return $this->databaseManager->transaction(
            callback: function () use ($attributes) {
                return $this->query?->create($attributes);
            }, attempts: 3);
    }

    /**
     * @param  array<string,mixed>  $attributes
     *
     * @throws Throwable
     */
    public function update(int $id, array $attributes, bool $passNull = false): ?Model
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
     * @param  int  $id
     *
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
