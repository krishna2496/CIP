<?php

namespace Tests\Unit\Libraries\PaymentGateway\Stripe\Events;

use App\Libraries\Amount;
use App\Libraries\PaymentGateway\PaymentGatewayDetailedTransaction;
use App\Libraries\PaymentGateway\PaymentGatewayDetailedTransfer;
use App\Libraries\PaymentGateway\PaymentGatewayFactory;
use App\Libraries\PaymentGateway\Stripe\Events\PaymentEvent;
use App\Libraries\PaymentGateway\Stripe\StripePaymentGateway;
use Illuminate\Support\Collection;
use Mockery;
use TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PaymentEventTest extends TestCase
{
    /*
     * @var Event
     */
    private $event;

    /*
     * @var StripePaymentGateway
     */
    private $stripePayment;

    public function setUp(): void
    {
        $this->stripePayment = $this->mock(StripePaymentGateway::class);

        $payment = $this->mock('overload:App\Libraries\PaymentGateway\PaymentGatewayFactory');
        $payment->shouldReceive('getPaymentGateway')
            ->once()
            ->andReturn($this->stripePayment);

        $this->event = PaymentEvent::constructFrom(
            $this->eventData()
        );
    }

    /**
     * @testdox Test data and charge method on PaymentEvent Class
     */
    public function testDataCharge()
    {
        $data = $this->flattenArrayKeys($this->eventData());

        foreach ($data as $path => $value) {
            if (!strstr($path, 'data.object')) {
                $this->assertSame($value, $this->event->$path);
                continue;
            }
            $path = str_replace('data.object.', null, $path);
            $method ='getData';
            if (strstr($path, 'charges.data.0')) {
                $method = 'getCharge';
                $path = str_replace('charges.data.0.', null, $path);
            }
            $this->assertSame($value, $this->event->$method($path));
        }
    }

    /**
     * @testdox Test transaction method on PaymentEvent Class
     */
    public function testTransaction()
    {
        $transactionId = $this->eventData()['data']['object']['charges']['data'][0]['balance_transaction'];
        $transaction = new PaymentGatewayDetailedTransaction;

        $this->stripePayment
            ->shouldReceive('getTransaction')
            ->once()
            ->with($transactionId)
            ->andReturn($transaction);

        $result = $this->event->getTransaction();

        $this->assertEquals($result, (object) $transaction->toArray());
    }

    /**
     * @testdox Test transfer method on PaymentEvent Class
     */
    public function testTransfer()
    {
        $transferId = $this->eventData()['data']['object']['charges']['data'][0]['transfer'];
        $transfer = new PaymentGatewayDetailedTransfer;

        $this->stripePayment
            ->shouldReceive('getTransfer')
            ->once()
            ->with($transferId)
            ->andReturn($transfer);

        $result = $this->event->getTransfer();

        $this->assertEquals($result, (object) $transfer->toArray());
    }

    /**
     * @testdox Test status method on PaymentEvent Class
     */
    public function testStatus()
    {
        $type = config('constants.payment_statuses');

        $expected = [
            'canceled' => $type['CANCELED'],
            'processing' => $type['PENDING'],
            'requires_action' => $type['FAILED'],
            'requires_capture' => $type['FAILED'],
            'requires_confirmation' => $type['FAILED'],
            'requires_payment_method' => $type['FAILED'],
            'succeeded' => $type['SUCCESS']
        ];

        foreach ($expected as $status => $type) {
            $this->event->data->object->status = $status;
            $this->assertSame($type, $this->event->getStatus());
        }

    }

    /**
     * @testdox Test method method on PaymentEvent Class
     */
    public function testMethod()
    {
        $methodId = $this->eventData()['data']['object']['charges']['data'][0]['payment_method'];
        $method = $this->eventData()['data']['object']['charges']['data'][0]['payment_method_details']['card'];
        $expected = [
            'id' => $methodId,
            'card' => $method
        ];

        $result = $this->event->getMethod();
        $this->assertEquals($result, $expected);
    }

    /**
     * Flatten array keys with values
     *
     * @param array $event
     * @param array $keys
     * @param string $field
     *
     * @return array
     */
    private function flattenArrayKeys($event, $keys = [], $field = null)
    {
        $keys = $keys;
        foreach ($event as $key => $value) {
            $fieldKey = $field.($field ? '.' : null).$key;
            if (is_array($value)) {
                $keys = array_merge($this->flattenArrayKeys($value, $keys, $fieldKey));
                continue;
            }
            $keys[$fieldKey] = $value;
        }
        return $keys;
    }

    /**
     * Get sample event data that the method will receive
     *
     * @return array
     */
    private function eventData()
    {
        return [
            'id' => 'evt_00000000000000',
            'type' => 'payment_intent.payment_failed',
            'data' => [
              	'object' => [
                    'id' => 'pi_00000000000000',
                    'object' => 'payment_intent',
                    'charges' => [
                        'object' => 'list',
                        'data' => [
                            [
                                'id' => 'ch_0000000000',
                                'object' => 'charge',
                                'amount' => 10553,
                                'balance_transaction' => 'txn_0000000000000000',
                                'billing_details' => [
                                    'address' => [
                                        'city' => 'Cityqs',
                                        'country' => 'BE',
                                        'line1' => 'line 2',
                                        'line2' => 'line 3',
                                        'postal_code' => '11111',
                                        'state' => 'state',
                                    ],
                                    'email' => '0000000@optimy.com',
                                    'name' => 'Sample name',
                                    'phone' => null,
                                ],
                                'payment_method' => 'pm_00000000000',
                                'payment_method_details' => [
                                    'card' => [
                                        'brand' => 'visa',
                                        'checks' => [
                                            'address_line1_check' => 'pass',
                                            'address_postal_code_check' => 'pass',
                                            'cvc_check' => null,
                                        ],
                                        'country' => 'US',
                                        'exp_month' => 12,
                                        'exp_year' => 2024,
                                        'fingerprint' => '0000000000',
                                        'funding' => 'credit',
                                        'installments' => null,
                                        'last4' => '4242',
                                        'network' => 'visa',
                                        'three_d_secure' => [
                                            'authenticated' => false,
                                            'authentication_flow' => null,
                                            'result' => 'attempt_acknowledged',
                                            'result_reason' => null,
                                            'succeeded' => true,
                                            'version' => '1.0.2',
                                        ],
                                        'wallet' => null,
                                    ],
                                    'type' => 'card',
                                ],
                                'status' => 'succeeded',
                                'transfer' => 'tr_000000000000',
                                'transfer_data' => [
                                    'amount' => 10000,
                                    'destination' => 'acct_000000000',
                                ]
                            ]
                        ],
                        'has_more' => false,
                        'url' => '/v1/charges?payment_intent=0000000000000000'
                    ],
                    'metadata' => [
                      'tenant_id' => '34',
                      'mission_id' => '10',
                      'organization_id' => '9012929-ASDASD9AA-ASDASD-ASDASD-ASD'
                    ],
                    'status' => 'requires_payment_method',
                    'transfer_data' => null,
                    'transfer_group' => null
                ]
            ]
        ];
    }

    /**
    * Mock an object
    *
    * @param string name
    *
    * @return Mockery
    */
    private function mock($class)
    {
        return Mockery::mock($class);
    }

}
