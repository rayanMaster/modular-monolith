<?php

namespace App\Http\Controllers;

use App\DTO\WarehouseCreateDTO;
use App\Http\Requests\WarehouseCreateRequest;
use App\Models\WareHouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{

    public function list()
    {

    }

    public function store(WarehouseCreateRequest $request){
        /**
         * @var array{
         *     name:string,
         *     address_id:int|null
         * }$requestedData
         */
       $requestedData = WarehouseCreateDTO::fromRequest($request->validated());
       $warehouse = Warehouse::query()->create();

    }
}
