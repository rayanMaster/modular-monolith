<?php

use Tests\RefreshDatabaseWithSeed;

pest()
    ->use(RefreshDatabaseWithSeed::class)
    ->in(
        './Feature',
    )->beforeEach(function () {
        Http::preventStrayRequests();
    });
