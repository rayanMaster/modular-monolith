<?php

namespace App\Repository;

use App\Models\WorkSite;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;

readonly class WorkSiteRepository extends MainRepository
{
    public function __construct(Builder | null $query, DatabaseManager $databaseManager)
    {
        $query = WorkSite::query();
        parent::__construct($query, $databaseManager);
    }
}
