<?php

describe('Order Create', function () {
    test('As a worksite manager/admin, I can create an order for a worksite or general one', function () {

    });
});
describe('Order Update', function () {
    test('As a worksite manager, I can update an order items, while its pending', function () {
    });
    test('As a worksite manager, I cant update an order items, while its processed', function () {
    });
    test('As an admin i can update an order items at any status', function () {
    });
});

describe('Order List', function () {
    test('As a worksite manager, I can see list of my orders', function () {
    });
    test('As an admin, I can see list of all orders in the system', function () {
    });
});

describe('Order Detail', function () {
    test('As a worksite manager, I can see details of my order', function () {
    });
    test('As an admin, I can see details of any order in the system', function () {
    });
});

describe('Order Status', function () {
    test('As a worksite manager, I can see the status of the order', function () {
    });
    test('As a worksite manager, I can update the status of the order to Delivered only', function () {
    });
    test('As a store keeper, I can update the status of the order to Received only', function () {
    });
    test('As an admin, I can update the status of the order to any status', function () {
    });
    test('As an admin, I can see the status of the order in the system', function () {
    });
    test('As an admin, I should receive notifications with all order statuses in the system', function () {
    });
    test('As an worksite manager, I should received notification with my orders status', function () {
    });
});
