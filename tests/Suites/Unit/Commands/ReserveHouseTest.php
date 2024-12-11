<?php

namespace App\Tests\Suites\Unit\Commands;

use App\Application\Commands\ReserveHouse\ReserveHouseCommand;
use App\Application\Commands\ReserveHouse\ReserveHouseCommandHandler;
use App\Application\Exception\BadRequestException;
use App\Application\Exception\NotFoundException;
use App\Domain\Entity\EntryStatus;
use App\Domain\Entity\House;
use App\Domain\Entity\HouseCalendar;
use App\Domain\Entity\Reservation;
use App\Domain\Entity\ReservationStatus;
use App\Domain\Entity\User;
use App\Domain\Model\AuthenticatedUser;
use App\Infrastructure\ForTests\Repositories\RamHouseCalendarRepository;
use App\Infrastructure\ForTests\Repositories\RamHouseRepository;
use App\Infrastructure\ForTests\Repositories\RamReservationRepository;
use App\Infrastructure\ForTests\Repositories\RamUserRepository;
use App\Infrastructure\ForTests\Services\FixedAuthenticatedUserProvider;
use App\Infrastructure\ForTests\Services\FixedIdProvider;
use App\Infrastructure\ForTests\Services\RamMailer;
use DateTime;
use PHPUnit\Framework\TestCase;

class ReserveHouseTest extends TestCase {
  private AuthenticatedUser $user;

  private FixedIdProvider $idProvider;
  private FixedAuthenticatedUserProvider $userProvider;
  private RamReservationRepository $reservationRepository;
  private RamHouseRepository $houseRepository;
  private RamUserRepository $userRepository;
  private RamHouseCalendarRepository $houseCalendarRepository;
  private RamMailer $mailer;

  private ReserveHouseCommandHandler $commandHandler;

  protected function setUp(): void {
    $this->user = new AuthenticatedUser("user-id");

    $house = new House("house-id", "owner-id");

    $reservation = new Reservation(
      "rersevation-1",
      "house-id",
      "user-id",
      new DateTime("2024-01-05"),
      new DateTime("2024-01-06"),
      ReservationStatus::ACCEPTED
    );

    $houseCalendar = new HouseCalendar("house-id");
    $houseCalendar->addReservation($reservation);

    $this->idProvider = new FixedIdProvider("reservation-id");
    $this->userProvider = new FixedAuthenticatedUserProvider($this->user);
    $this->reservationRepository = new RamReservationRepository([$reservation]);
    $this->houseRepository = new RamHouseRepository([$house]);
    $this->userRepository = new RamUserRepository([
      User::create("owner-id", "owner@gmail.com", "azerty")
    ]);
    $this->houseCalendarRepository = new RamHouseCalendarRepository([$houseCalendar]);
    $this->mailer = new RamMailer();

    $this->commandHandler = new ReserveHouseCommandHandler(
      $this->idProvider,
      $this->reservationRepository,
      $this->houseRepository,
      $this->userProvider,
      $this->userRepository,
      $this->houseCalendarRepository,
      $this->mailer
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
    $this->assertEquals(ReservationStatus::PENDING, $reservation->getStatus());
  }

  public function test_happyPath_shouldReserveCalendarEntry() {
    $response = $this->commandHandler->execute(
      new ReserveHouseCommand(
        "house-id",
        "2024-01-01",
        "2024-01-02"
      )
    );

    $houseCalendar = $this->houseCalendarRepository->findById("house-id");

    $entry = $houseCalendar->findEntryById($response->getId());

    $this->assertNotNull($entry);
    $this->assertEquals("2024-01-01", $entry->getStartDate()->format("Y-m-d"));
    $this->assertEquals("2024-01-02", $entry->getEndDate()->format("Y-m-d"));
    $this->assertEquals(EntryStatus::PENDING, $entry->getStatus());
  }

  public function test_happyPath_shouldSendEmailToOwner() {
    $response = $this->commandHandler->execute(
      new ReserveHouseCommand(
        "house-id",
        "2024-01-01",
        "2024-01-02"
      )
    );

    $this->assertCount(1, $this->mailer->inbox);

    $email = $this->mailer->inbox[0];

    $this->assertEquals("Nouvelle réservation", $email->getSubject());
    $this->assertEquals("owner@gmail.com", $email->getTo()[0]->getAddress());
  }

  public function test_whenHouseNotDefined_shouldFail() {
    $command = new ReserveHouseCommand(
      "not-found-id",
      "2024-01-01",
      "2024-01-02"
    );

    try {
      $this->commandHandler->execute($command);
      $this->fail("The house must exist");
    } catch (\Exception $e) {
      $this->assertInstanceOf(NotFoundException::class, $e);
      $this->assertEquals("House not found", $e->getMessage());
    }
  }

  public function test_whenHouseIsUnavailable_shouldFail() {
    $command = new ReserveHouseCommand(
      "house-id",
      "2024-01-05",
      "2024-01-06"
    );

    try {
      $this->commandHandler->execute($command);
      $this->fail("The house must be available");
    } catch (\Exception $e) {
      $this->assertEquals("House not available", $e->getMessage());
      $this->assertInstanceOf(BadRequestException::class, $e);
    }
  }
}