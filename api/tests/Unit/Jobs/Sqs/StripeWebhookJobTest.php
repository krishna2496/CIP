<?php

namespace Tests\Unit\Jobs\Sqs;

use App\Helpers\Helpers;
use App\Jobs\Job;
use App\Jobs\Sqs\StripeWebhookJob;
use App\Libraries\PaymentGateway\PaymentGatewayDetailedTransaction;
use App\Libraries\PaymentGateway\PaymentGatewayDetailedTransfer;
use App\Libraries\PaymentGateway\Stripe\Events\Event;
use App\Libraries\PaymentGateway\Stripe\Events\PaymentEvent;
use App\Libraries\PaymentGateway\Stripe\StripePaymentGateway;
use App\Models\PaymentGateway\Payment;
use App\Services\PaymentGateway\PaymentService;
use Exception;
use Illuminate\Contracts\Queue\Job as QueueJob;
use Illuminate\Support\Facades\Log;
use Mockery;
use TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class StripeWebhookJobTest extends TestCase
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
     * App\Jobs\Sqs\StripeWebhookJob
     */
    private $stripeWebhookJob;

    public function setUp(): void
    {
        parent::setUp();
        $this->paymentService = $this->mock(PaymentService::class);
        $this->helpers = $this->mock(Helpers::class);

        $this->stripeWebhookJob = new StripeWebhookJob(
            $this->paymentService,
            $this->helpers
        );
    }

    /**
     * @testdox Test handle method on Event Class
     */
    public function testHandle()
    {
        $data = $this->eventData();
        $queueJob = $this->mock(QueueJob::class);
        $tenantId = $data['data']['object']['metadata']['tenant_id'];
        $transactionId = $data['data']['object']['charges']['data'][0]['balance_transaction'];
        $transferId = $data['data']['object']['charges']['data'][0]['transfer'];

        $stripePayment = $this->mock(StripePaymentGateway::class);
        $stripePayment
            ->shouldReceive('getTransaction')
            ->once()
            ->with($transactionId)
            ->andReturn(new PaymentGatewayDetailedTransaction);
        $stripePayment
            ->shouldReceive('getTransfer')
            ->once()
            ->with($transferId)
            ->andReturn(new PaymentGatewayDetailedTransfer);

        $payment = $this->mock('overload:App\Libraries\PaymentGateway\PaymentGatewayFactory');
        $payment->shouldReceive('getPaymentGateway')
            ->once()
            ->andReturn($stripePayment);

        $this->helpers
            ->shouldReceive('createConnection')
            ->once()
            ->with((int) $tenantId)
            ->andReturn(true);

        $this->paymentService
            ->shouldReceive('update')
            ->once()
            ->andReturn(true);

        Log::shouldReceive('info')
            ->with('STRIPE EVENT:', $data);

        $response = $this->stripeWebhookJob->handle(
            $queueJob,
            $data
        );

        $this->assertSame($response, true);
    }

    /**
     * @testdox Test handle method on Event Class with exception
     */
    public function testHandleException()
    {
        $this->expectException(Exception::class);

        $data = [];
        $queueJob = $this->mock(QueueJob::class);

        Log::shouldReceive('info')
            ->with('STRIPE EVENT:', $data);

        $response = $this->stripeWebhookJob->handle(
            $queueJob,
            $data
        );
    }

    /**
     * @testdox Test handle method on Event Class with tenant mission exception
     */
    public function testHandleTenantException()
    {
        $this->expectException(Exception::class);

        $data = $this->eventData();
        $data['data']['object']['metadata']['tenant_id'] = null;
        $queueJob = $this->mock(QueueJob::class);

        Log::shouldReceive('info')
            ->with('STRIPE EVENT:', $data);

        $response = $this->stripeWebhookJob->handle(
            $queueJob,
            $data
        );
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
