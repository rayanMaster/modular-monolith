<?php

namespace App\Listeners;

use App\Events\PaymentCreatedEvent;
use App\Http\Integrations\Accounting\Connector\AccountingConnector;
use App\Http\Integrations\Accounting\Requests\PaymentSync\PaymentSyncDTO;
use App\Http\Integrations\Accounting\Requests\PaymentSync\PaymentSyncRequest;
use App\Models\Worksite;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

readonly class PaymentCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private AccountingConnector $accountingConnector
    )
    {
        //
    }

    /**
     * Handle the event.
     * @throws RequestException
     */
    public function handle(PaymentCreatedEvent $event): void
    {
        //{"payable_type":"worksite",
        //"payable_id":126,
        //"amount":3000,
        //"payment_date":"2024-04-12T10:34:00.000000Z",
        //"payment_type":1,
        foreach ($event->payments as $payment) {
            $uuid = null;
            if ($payment->payable instanceof Worksite) {
                $uuid = $payment->payable->uuid;
            }
            $paymentSyncDTO = new PaymentSyncDTO(
                customer_uuid: $event->customerUUID,
                worksite_uuid: $uuid,
                payment_date: $payment['payment_date'],
                payment_amount: $payment['amount']
            );
            $paymentSyncRequest = new PaymentSyncRequest($paymentSyncDTO);
            try {
                $this->accountingConnector->send($paymentSyncRequest);
            } catch (FatalRequestException $e) {
                Log::info('error', [$e->getMessage()]);
            }
        }
    }
}
