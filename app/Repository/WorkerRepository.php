<?php

namespace App\Repository;

use App\Models\User;
use Illuminate\Database\DatabaseManager;

/**
 * @extends MainRepository<User>
 */
readonly class WorkerRepository extends MainRepository
{
    public function __construct(DatabaseManager $databaseManager)
    {
        $query = User::query();
        parent::__construct($query, $databaseManager);
    }
}
