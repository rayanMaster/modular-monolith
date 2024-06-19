<?php

namespace App\Repository;

use App\Models\Contractor;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends MainRepository<Contractor>
 */
readonly class ContractorRepository extends MainRepository
{
    public function __construct(DatabaseManager $databaseManager)
    {
        $query = Contractor::query()->with(['address']);
        parent::__construct($query, $databaseManager);
    }
}
