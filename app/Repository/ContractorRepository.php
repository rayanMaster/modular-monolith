<?php

namespace App\Repository;

use App\Models\Contractor;
use App\Repository\MainRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;

readonly class ContractorRepository extends MainRepository
{
    public function __construct(Builder $query, DatabaseManager $databaseManager)
    {
        $query = Contractor::query();
        parent::__construct($query, $databaseManager);
    }
}
