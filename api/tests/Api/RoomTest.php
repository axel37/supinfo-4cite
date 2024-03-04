<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Hotel;
use App\Repository\HotelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Uid\Uuid;

class RoomTest extends ApiTestCase
{
    private ContainerInterface $container;
    private HotelRepository $hotelRepository;
    private EntityManagerInterface $em;
    private Uuid $hotelId;
    protected function setUp(): void
    {
        parent::setUp();
        $this->container = self::getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $this->hotelRepository = $this->container->get(HotelRepository::class);

        $hotel = new Hotel('Grand hotel', 'Pine street');
        $this->hotelId = $hotel->getId();
        $this->em->persist($hotel);
        $this->em->flush();
    }

    public function testCanCreateRoom(): void
    {
        static::createClient()->request('POST', '/rooms', [
            'json' => [
                'hotelId' => $this->hotelId,
                'name' => 'Room 237',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains([
            '@context' => '/contexts/Room',
            '@type' => 'Room',
            'name' => 'Room 237',
        ]);
    }

    public function testFailsOnInvalidName(): void
    {
        static::createClient()->request('POST', '/rooms', [
            'json' => [
                'hotelId' => $this->hotelId,
                'name' => '',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    public function testFailsOnNoHotelProvided(): void
    {
        static::createClient()->request('POST', '/rooms', [
            'json' => [
                'hotelId' => '',
                'name' => 'Room ABC',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    public function xtestCreateThenFindRoom(): void
    {
        // TODO : Implement this. Pre-requisite : must load a hotel.
        $response = static::createClient()->request('POST', '/rooms', [
            'json' => [
                'name' => 'Room 237',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        // Get id of newly created room from response


    }

}
