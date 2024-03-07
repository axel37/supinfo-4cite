<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserTest extends ApiTestCase
{
    private ContainerInterface $container;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $hasher;
    protected function setUp(): void
    {
        parent::setUp();
        $this->container = self::getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $this->userRepository = $this->container->get(UserRepository::class);
        $this->hasher = $this->container->get('security.user_password_hasher');
    }


    public function testCanCreateUser(): void
    {
        static::createClient()->request('POST', '/users', [
            'json' => [
                'email' => 'user@example.com',
                'userName' => 'UserName',
                'plainPassword' => 'password'
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains([
            '@context' => '/contexts/User',
            '@type' => 'User',
            'email' => 'user@example.com',
            'userName' => 'UserName',
        ]);
    }

    public function disabled_testLogin(): void
    {
        $client = self::createClient();

        $user = new User('testing@example.com', 'Testing User');
        $user->setPassword(
            $this->hasher->hashPassword($user, '$3CR3T')
        );

        $this->em->persist($user);
        $this->em->flush();

        // retrieve a token
        $response = $client->request('POST', '/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'testing@example.com',
                'password' => '$3CR3T',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);


        // test not authorized
        $client->request('GET', '/hotels');
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/hotels', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }

}
