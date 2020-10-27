<?php

namespace Tests\Unit\Repositories\Donation;

use App\Models\Donation;
use App\Repositories\Donation\DonationRepository;
use Faker\Factory as FakerFactory;
use Mockery;
use TestCase;

class DonationRepositoryTest extends TestCase
{
    /**
     * @var App\Repositories\Donation\DonationRepository
     */
    private $repository;

    /**
     * @var App\Models\Donation
     */
    private $donation;

    /**
     * @var Faker
     */
    private $faker;


    public function setUp(): void
    {
        $this->donation = $this->mock(Donation::class);
        $this->faker = FakerFactory::create();

        $this->repository = new DonationRepository(
            $this->donation
        );
    }

    /**
     * @testdox Test create method on DonationRepository class
     */
    public function testCreate()
    {
        $data = [
            'mission_id' => rand(0, 100),
            'payment_id' => $this->faker->uuid,
            'organization_id' => $this->faker->uuid,
            'user_id' => 1
        ];

        $donation = (new Donation())
            ->setAttribute('mission_id', $data['mission_id'])
            ->setAttribute('payment_id', $data['payment_id'])
            ->setAttribute('organization_id', $data['organization_id'])
            ->setAttribute('user_id', $data['user_id']);

        $this->donation
            ->shouldReceive('create')
            ->once()
            ->with($donation->getAttributes())
            ->andReturn($donation);

        $response = $this->repository->create($donation);

        $this->assertSame($response, $donation);
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
