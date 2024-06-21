<?php

use App\Models\Address;
use App\Models\User;
use App\Models\WorkSite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{assertSoftDeleted, getJson, postJson, putJson, actingAs, assertDatabaseCount};
use \Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

describe('Warehouse Items', function() {
    beforeEach(function () {
        $this->artisan('storage:link');
        $this->workSite = WorkSite::factory()->create();
        $this->admin = User::factory()->admin()->create();
        $this->notAdmin = User::factory()->worker()->create();
        $this->address = Address::factory()->create();

    });
    it('should return error if adding same item twice to a warehouse', function() {});
    it('should prevent adding negative quantity fot an item', function() {});
    test('adding items to warehouse from a supplier', function() {});
    test('moving item from one warehouse to other', function() {});
    test('updating quantity and price for multiple items', function() {});
    test('getting list of low stock items', function() {});
    test('getting list of out off stock items', function() {});

});



//    it('should have the option to attach a wareHouse with a workSite or make it as main workHouse
//    without a workSite', function () {
//    });
//    it('should assign store keeper to a wareHouse, and at least one for each', function () {
//    });
//    it('should be able to move items between warehouses and make data consist between them', function () {
//    });
//    it('should be able to add new items to a warehouse from any external supplier', function () {
//    });
//    it('should track all movements between warehouses', function () {
//    });
//    it('should move items to a workSite from its own warehouse only', function () {
//    });
//    it('should track all items movements between worksite and its warehouse', function () {
//    });
//
//    test('if items entered to a worksite are the same the drop off its wareHouse', function () {
//    });
//});

