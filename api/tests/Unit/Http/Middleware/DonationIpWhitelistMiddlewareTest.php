<?php

namespace Tests\Unit\Http\Middleware;

use App\Helpers\IPValidationHelper;
use App\Helpers\ResponseHelper;
use App\Http\Middleware\DonationIpWhitelistMiddleware;
use App\Models\DonationIpWhitelist;
use App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository;
use App\Services\DonationIp\WhitelistService;
use App\Traits\RestExceptionHandlerTrait;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use TestCase;

class DonationIpWhitelistMiddlewareTest extends TestCase
{
    /**
     * @var App\Services\DonationIp\WhitelistService
     */
    private $whitelistService;

    /**
     * @var App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository
     */
    private $tenantActivatedSettingRepository;

    /**
     * @var App\Helpers\IPValidationHelper
     */
    private $ipValidationHelper;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Http\Middleware\DonationIpWhitelistMiddleware
     */
    private $donationIpWhitelistMiddleware;

    public function setUp(): void
    {
        parent::setUp();
        $this->whitelistService = $this->mock(WhitelistService::class);
        $this->tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $this->ipValidationHelper = $this->mock(IPValidationHelper::class);
        $this->responseHelper = $this->mock(ResponseHelper::class);

        $this->donationIpWhitelistMiddleware = new DonationIpWhitelistMiddleware(
            $this->whitelistService,
            $this->tenantActivatedSettingRepository,
            $this->ipValidationHelper,
            $this->responseHelper
        );
    }

    /**
     * @testdox Test IP whitelisted - PASS
     */
    public function testWhitelistedIpAddress()
    {
        $request = new Request();
        $request->server->add(['REMOTE_ADDR' => '192.168.1.10']);

        $this->tenantActivatedSettingRepository
            ->shouldReceive('checkTenantSettingStatus')
            ->with(
                'donation_ip_whitelist',
                $request
            )
            ->andReturn(true);

        $whitelistedIps = $this->whitelistedIps();
        $this->whitelistService
            ->shouldReceive('getList')
            ->with(
                ['perPage' => null],
                ['search' => null, 'order' => null]
            )
            ->andReturn($whitelistedIps);

        $whitelists = array_column($whitelistedIps->toArray(), 'pattern');
        $this->ipValidationHelper
            ->shouldReceive('verify')
            ->with(
                $request->ip(),
                $whitelists
            )
            ->andReturn(true);

        $this->responseHelper
            ->shouldReceive('error')
            ->never();

        $this->donationIpWhitelistMiddleware->handle($request, function (){});
    }

    /**
     * Returns Collection of DonationIpWhitelist
     *
     * @return array
     */
    private function whitelistedIps()
    {
        return new Collection([
            factory(DonationIpWhitelist::class)->make(),
            factory(DonationIpWhitelist::class)->make()
        ]);
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