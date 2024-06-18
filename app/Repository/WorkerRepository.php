<?php

namespace App\Repository;

use App\Models\Employee;
use Illuminate\Database\DatabaseManager;

/**
 * @extends MainRepository<Employee>
 */
readonly class WorkerRepository extends MainRepository
{
    public function __construct(DatabaseManager $databaseManager)
    {
        $query = Employee::query();
        parent::__construct($query, $databaseManager);
    }
}
