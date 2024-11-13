<?php

use Tests\RefreshDatabaseWithSeed;
use Saloon\Config;

pest()
    ->use(RefreshDatabaseWithSeed::class)
    ->in(
        './Feature',
    )->beforeEach(function () {
        Http::preventStrayRequests();
    });
