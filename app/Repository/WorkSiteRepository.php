<?php

namespace App\Repository;

use App\Models\WorkSite;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends MainRepository<WorkSite>
 */
readonly class WorkSiteRepository extends MainRepository
{
    public function __construct(DatabaseManager $databaseManager)
    {
        $query = WorkSite::query();
        parent::__construct($query, $databaseManager);
    }
}
