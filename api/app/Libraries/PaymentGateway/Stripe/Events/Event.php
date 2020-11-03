<?php

namespace App\Libraries\PaymentGateway\Stripe\Events;

use App\Libraries\Amount;
use App\Libraries\PaymentGateway\PaymentGatewayFactory;
use Stripe\Event as StripeEvent;

class Event extends StripeEvent
{
    /**
     * Stripe events
     */
    const TYPES = [
        'PAYMENT_FAILED' => 'payment_intent.payment_failed',
        'PAYMENT_SUCCESS' => 'payment_intent.succeeded'
    ];

    /**
     * Connect to stripe payment gateway
     */
    protected $connect = false;

    /**
     * App\Libraries\PaymentGateway\PaymentGatewayFactory
     */
    protected $paymentGateway;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->connect) {
            $this->stripeGateway();
        }
    }

    /**
     * Get event data object
     *
     * @param string $path key path
     *
     * @return mixed
     */
    public function getDataObject($path = null)
    {
        $dataObject = $this->data->object;

        return $this->getPath($dataObject, $path);
    }

    /**
     * Setup payment gateway
     *
     * @return void
     */
    private function stripeGateway()
    {
        $this->paymentGateway = (new PaymentGatewayFactory())->getPaymentGateway(
            config('constants.payment_gateway_types.STRIPE')
        );
    }

    /**
     * Get event data based on given path
     *
     * @param object $data
     * @param string $path key path
     *
     * @return mixed
     */
    protected function getPath($data, $path)
    {
        if (!$path) {
            return $data;
        }

        $value = $data;
        foreach (explode('.', $path) as $key) {
            $value = $value->$key ?? null;
        }

        return $value;
    }
}