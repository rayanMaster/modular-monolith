<?php

namespace App\Services\Worksite;

use App\Enums\WarehouseItemThresholdsEnum;
use App\Models\Item;
use App\Models\Worksite;
use App\Services\PaymentSyncService;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

class WorksiteService
{

    public function __construct(
        private readonly PaymentSyncService $paymentSyncService
    )
    {

    }

    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws \JsonException
     */
    public function getDetails(int $id)
    {
        $worksite = Worksite::query()->with(['customer', 'items.warehouse', 'media', 'address', 'pendingOrders'])
            ->findOrFail($id);

        $payments = $this->paymentSyncService->getPaymentsForWorksite($worksite);


        $worksite->customerPayments = $payments;

        $worksite->totalPaymentsAmount = number_format((float)$payments->sum('amount'), 2);


        $worksite->items->map(function (Item $item) {
            $item->quantityInWarehouse = $item->warehouse->quantity;
            $item->inStock = $item->warehouse->quantity > WarehouseItemThresholdsEnum::LOW->value ?
                'In-Stock' :
                ($item->warehouse->quantity > 0 ? 'Low-Stock' : 'Out-OFF-Stock');
        });

        return $worksite;
    }

}
