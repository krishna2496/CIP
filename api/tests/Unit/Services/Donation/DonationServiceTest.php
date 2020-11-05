<?php

namespace Tests\Unit\Services\Donation;

use App\Models\Donation;
use App\Repositories\Donation\DonationRepository;
use App\Services\Donation\DonationService;
use Faker\Factory as FakerFactory;
use Mockery;
use TestCase;

class DonationServiceTest extends TestCase
{
    /**
     * @var App\Repositories\Donation\DonationRepositoryTest
     */
    private $repository;

    /**
     * @var App\Models\Donation
     */
    private $donation;

    /**
     * @var App\Services\Donation\DonationService
     */
    private $service;

    /**
     * @var Faker
     */
    private $faker;

    public function setUp(): void
    {
        $this->repository = $this->mock(DonationRepository::class);
        $this->donation = $this->mock(Donation::class);
        $this->faker = FakerFactory::create();

        $this->service = new DonationService(
            $this->repository
        );
    }

    /**
     * @testdox Test create method on DonationService class
     */
    public function testCreate()
    {
        $expected = (new Donation())
            ->setAttribute('id', $this->faker->uuid)
            ->setAttribute('mission_id', rand(1, 100))
            ->setAttribute('payment_id', $this->faker->uuid)
            ->setAttribute('organization_id', $this->faker->uuid)
            ->setAttribute('user_id', 1);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with($this->donation)
            ->andReturn($expected);

        $response = $this->service->create(
            $this->donation
        );

        $this->assertSame($response, $expected);
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
