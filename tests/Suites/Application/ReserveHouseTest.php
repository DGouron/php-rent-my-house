<?php

namespace App\Tests\Suites\Application;

use App\Application\Ports\Repositories\IHouseRepository;
use App\Application\Ports\Repositories\IReservationRepository;
use App\Application\Ports\Repositories\IUserRepository;
use App\Domain\Entity\House;
use App\Domain\Entity\User;
use App\Tests\Infrastructure\ApplicationTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ReserveHouseTest extends ApplicationTestCase {
  public function test_happyPath() {
    $client = self::initialize();

    $user = new User();
    $user->setId("user-id");
    $user->setEmailAddress("johndoe@gmail.com");
    $user->setPassword("azerty");

    $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
    $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

    /** @var IUserRepository $userRepository */
    $userRepository = self::getContainer()->get(IUserRepository::class);
    $userRepository->save($user);

    $client->loginUser($user);

    /** @var IHouseRepository $houseRepository */
    $houseRepository = self::getContainer()->get(IHouseRepository::class);
    $houseRepository->save(new House("house-id"));

   $this->request('POST', '/api/reserve-house', [
     'houseId' => 'house-id',
     'startDate' => '2022-01-01',
     'endDate' => '2022-01-02',
   ]);

    $this->assertResponseStatusCodeSame(200);

    $response = $client->getResponse();
    $data = json_decode($response->getContent(), true);

    $id = $data['id'];

    /** @var IReservationRepository $reservationRepository */
    $reservationRepository = self::getContainer()->get(IReservationRepository::class);
    $reservation = $reservationRepository->findById($id);

    $this->assertNotNull($reservation);

    $this->assertEquals("house-id", $reservation->getHouseId());
    $this->assertEquals("user-id", $reservation->getTenantId());
    $this->assertEquals("2022-01-01", $reservation->getStartDate()->format('Y-m-d'));
    $this->assertEquals("2022-01-02", $reservation->getEndDate()->format('Y-m-d'));
  }
}