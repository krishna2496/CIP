<?php

namespace App\Jobs\Sqs;

use App\Helpers\Helpers;
use App\Jobs\Job;
use App\Libraries\PaymentGateway\Stripe\Events\Event;
use App\Libraries\PaymentGateway\Stripe\Events\PaymentEvent;
use App\Models\PaymentGateway\Payment;
use App\Services\PaymentGateway\PaymentService;
use Illuminate\Contracts\Queue\Job as QueueJob;
use Illuminate\Support\Facades\Log;
use Exception;
use DB;

class StripeWebhookJob extends Job
{
    /**
     * App\Services\PaymentGateway\PaymentService
     */
    private $paymentService;

    /**
     * App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new job instance.
     *
     * @param App\Services\PaymentGateway\PaymentService $paymentService
     * @param App\Helpers\Helpers $helpers
     *
     * @return void
     */
    public function __construct(
        PaymentService $paymentService,
        Helpers $helpers
    ) {
        $this->paymentService = $paymentService;
        $this->helpers = $helpers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(QueueJob $job, array $data)
    {
        Log::info('STRIPE EVENT:', $data);

        $event = Event::constructFrom($data);
        if (!isset($event->type)) {
            throw new Exception('Missing event type.');
        }

        $tenantId = $event->getDataObject('metadata.tenant_id');
        if (!$tenantId) {
            throw new Exception('Missing metadata tenant ID.');
        }

        // Configure application to access tenant database
        $this->helpers->createConnection((int) $tenantId);

        switch ($event->type) {
            case Event::TYPES['PAYMENT_FAILED']:
            case Event::TYPES['PAYMENT_SUCCESS']:
                return $this->processPayment($event);
            break;
            default:
                throw new Exception('Unsupported event type.');
        }
    }

    /**
     * Handle stripe payment gateway events
     *
     * @param App\Libraries\PaymentGateway\Stripe\Events\Event $event
     *
     * @return bool
     */
    private function processPayment(Event $event)
    {
        $payment = PaymentEvent::constructFrom(
            $event->toArray()
        );

        $paymentMethodDetails = $payment->getCharge('payment_method_details.card');
        $paymentModel = new Payment();
        $paymentModel
            ->setAttribute('payment_gateway_payment_id', $payment->getData('id'))
            ->setAttribute('status', $payment->getStatus())
            ->setAttribute('payment_method_details', $payment->getMethod())
            ->setAttribute('transfer_currency', $payment->getTransaction('currency'))
            ->setAttribute('amount_converted', $payment->getTransaction('amount'))
            ->setAttribute('transfer_amount_converted', $payment->getTransfer('amount'))
            ->setAttribute('transfer_exchange_rate', $payment->getTransaction('exchange_rate'))
            ->setAttribute('payment_gateway_fee', $payment->getTransaction('fee'));

        // Only update billing data when payment success
        if ($payment->type === Event::TYPES['PAYMENT_SUCCESS']) {
            $paymentModel
                ->setAttribute('billing_phone', $payment->getCharge('billing_details.phone'))
                ->setAttribute('billing_address_line_1', $payment->getCharge('billing_details.address.line1'))
                ->setAttribute('billing_address_line_2', $payment->getCharge('billing_details.address.line2'))
                ->setAttribute('billing_city', $payment->getCharge('billing_details.address.city'))
                ->setAttribute('billing_state', $payment->getCharge('billing_details.address.state'))
                ->setAttribute('billing_postal_code', $payment->getCharge('billing_details.address.postal_code'));
        };

        return $this->paymentService->update($paymentModel);
    }
}
