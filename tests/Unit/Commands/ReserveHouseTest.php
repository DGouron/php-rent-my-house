<?php

namespace App\Tests\Unit\Commands;

use App\Application\Commands\ReserveHouse\ReserveHouseCommand;
use App\Application\Commands\ReserveHouse\ReserveHouseCommandHandler;
use App\Application\Ports\Repositories\IHouseRepository;
use App\Application\Ports\Repositories\IReservationRepository;
use App\Application\Ports\Services\IIdProvider;
use App\Application\Ports\Services\IUserProvider;
use App\Domain\Entity\House;
use App\Domain\Entity\Reservation;
use PHPUnit\Framework\TestCase;

class IdProvider implements IIdProvider {
  private string $id;

  public function __construct(string $id) {
    $this->id = $id;
  }

  public function getId() : string {
    return $this->id;
  }
}

class ReservationRepository implements IReservationRepository {
  private ?Reservation $reservation = null;

  public function save(Reservation $reservation) {
    $this->reservation = $reservation;
  }

  public function findById(string $id): ?Reservation {
    return $this->reservation;
  }
}

class HouseRepository implements IHouseRepository {
  private array $database = [];

  public function __construct(array $database = []) {
    $this->database = $database;
  }

  public function findById(string $id): ?House {
    foreach ($this->database as $house) {
      if ($house->getId() === $id) {
        return $house;
      }
    }

    return null;
  }
}

class AuthenticatedUser {
  private string $id;

  public function __construct(string $id) {
    $this->id = $id;
  }

  public function getId() : string {
    return $this->id;
  }
}

class AuthenticatedUserProvider implements IUserProvider {
  private AuthenticatedUser $user;

  public function __construct(AuthenticatedUser $user) {
    $this->user = $user;
  }

  public function getUser() : AuthenticatedUser {
    return $this->user;
  }
}

class ReserveHouseTest extends TestCase {
  private AuthenticatedUser $user;

  private IdProvider $idProvider;
  private AuthenticatedUserProvider $userProvider;
  private ReservationRepository $reservationRepository;
  private HouseRepository $houseRepository;

  private ReserveHouseCommandHandler $commandHandler;

  protected function setUp(): void {
    $this->user = new AuthenticatedUser("user-id");

    $this->idProvider = new IdProvider("reservation-id");
    $this->userProvider = new AuthenticatedUserProvider($this->user);
    $this->reservationRepository = new ReservationRepository();
    $this->houseRepository = new HouseRepository([
      new House("house-id")
    ]);

    $this->commandHandler = new ReserveHouseCommandHandler(
      $this->idProvider,
      $this->reservationRepository,
      $this->houseRepository,
      $this->userProvider
    );
  }

  public function test_happyPath_shouldCreateReservation() {
    $response = $this->commandHandler->execute(
      new ReserveHouseCommand(
        "house-id",
        "2024-01-01",
        "2024-01-02"
      )
    );

    $reservation = $this->reservationRepository->findById($response->getId());

    $this->assertNotNull($reservation);
    $this->assertEquals($response->getId(), $reservation->getId());
    $this->assertEquals("2024-01-01", $reservation->getStartDate()->format("Y-m-d"));
    $this->assertEquals("2024-01-02", $reservation->getEndDate()->format("Y-m-d"));
    $this->assertEquals("house-id", $reservation->getHouseId());
    $this->assertEquals("user-id", $reservation->getTenantId());
  }

  public function test_whenHouseNotDefined_shouldFail() {
    $command = new ReserveHouseCommand(
      "not-found-id",
      "2024-01-01",
      "2024-01-02"
    );

    try {
      $this->commandHandler->execute($command);
      $this->fail("The house should exist");
    } catch (\Exception $e) {
      $this->assertEquals("House not found", $e->getMessage());
    }
  }
}