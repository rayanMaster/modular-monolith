<?php

namespace App\Repository;

use App\Models\Employee;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;

readonly class WorkerRepository extends MainRepository
{
    public function __construct(Builder $query, DatabaseManager $databaseManager)
    {
        $query = Employee::query();
        parent::__construct($query, $databaseManager);
    }
}
