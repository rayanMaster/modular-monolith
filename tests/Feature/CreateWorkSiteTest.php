<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

//test('As an administrator, I want to create a main worksite,
//so that I can organize multiple sub-worksites under it', function () {
//
//
//
//    $wsCategory = WorkSiteCategory::factory()->create();
//
//    $customer = WorkSiteCustomer::factory()->create();
//
//    $workSiteResourceCategory = WorkSiteResourceCategory::factory()->create();
//
//    $workSiteResource1 = WorkSiteResource::factory()->create([
//        'resource_category_id' => $workSiteResourceCategory->id,
//    ]);
//    $workSiteResource2 = WorkSiteResource::factory()->create([
//        'resource_category_id' => $workSiteResourceCategory->id,
//    ]);
//
//    $workSite = WorkSite::factory()->create([
//        'customer_id' => $customer?->id,
//        'category_id' => $wsCategory?->id,
//    ]);
//
//    $workSite?->resources()->syncWithoutDetaching([
//        $workSiteResource1->id=>[ 'quantity' => 23, 'price' => 34],
//        $workSiteResource2->id=>[ 'quantity' => 4, 'price' => 43]
//    ]);
//
//    $payment = Payment::factory()->create([
//        'work_site_id' => $workSite?->id,
//    ]);
//
//    $workSiteResource3 = WorkSiteResource::factory()->create([
//        'resource_category_id' => $workSiteResourceCategory->id,
//    ]);
//
//    $workSite?->resources()->syncWithoutDetaching([
//         $workSiteResource1->id=>[ 'quantity' => 25, 'price' => 32],
//         $workSiteResource3->id=>[ 'quantity' => 20, 'price' => 20],
//    ]);
//
//
////    Media::create([
////        'model_id'=>$workSite->id,
////        'model_type'=>WorkSite::class,
////    ]);
//
//    expect($workSite->title)->toBe('main Work Site')
//        ->and($workSite->category?->title)->toBe('Main Category')
//        ->and($workSite->customer?->name)->toBe('Rayan')
//        ->and($workSite?->resources[0]->pivot->getAttributes())->toBe(
//            ["work_site_id" => 1,
//                "resource_id" => 1,
//                "quantity" => 25,
//                "price" => 32])
//        ->and($workSite?->resources[1]->pivot->getAttributes())->toBe(
//            ["work_site_id" => 1,
//                "resource_id" => 2,
//                "quantity" => 4,
//                "price" => 43])
//        ->and($workSite?->resources[2]->pivot->getAttributes())->toBe(
//            ["work_site_id" => 1,
//                "resource_id" => 3,
//                "quantity" => 20,
//                "price" => 20])
//        ->and($payment->work_site_id)->toBe($workSite->id)
//        ->and($payment->amount)->toBe(20)
//        ->and($payment->payment_type)->toBe(1);
//
//
//});
