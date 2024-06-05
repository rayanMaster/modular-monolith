<?php

namespace App\Repository;

use App\Models\Worker;
use App\Repository\MainRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;

readonly class WorkerRepository extends MainRepository
{
    public function __construct(Builder $query, DatabaseManager $databaseManager)
    {
        $query = Worker::query();
        parent::__construct($query, $databaseManager);
    }
}
