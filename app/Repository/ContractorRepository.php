<?php

namespace App\Repository;

use App\Models\Contractor;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;

readonly class ContractorRepository extends MainRepository
{
    public function __construct(Builder $query, DatabaseManager $databaseManager)
    {
        $query = Contractor::query()->with(['address']);
        parent::__construct($query, $databaseManager);
    }
}
