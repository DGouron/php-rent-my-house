<?php

namespace App\Tests\Suites\Unit\Commands;

use App\Application\Commands\AcceptReservation\AcceptReservationCommand;
use App\Application\Commands\AcceptReservation\AcceptReservationCommandHandler;
use App\Application\Commands\ReserveHouse\ReserveHouseCommandHandler;
use App\Domain\Entity\EntryStatus;
use App\Domain\Entity\House;
use App\Domain\Entity\Reservation;
use App\Domain\Entity\ReservationStatus;
use App\Domain\Entity\User;
use App\Domain\Model\AuthenticatedUser;
use App\Infrastructure\ForTests\Repositories\RamHouseRepository;
use App\Infrastructure\ForTests\Repositories\RamReservationRepository;
use App\Infrastructure\ForTests\Repositories\RamUserRepository;
use App\Infrastructure\ForTests\Services\FixedAuthenticatedUserProvider;
use App\Infrastructure\ForTests\Services\FixedIdProvider;
use App\Infrastructure\ForTests\Services\RamMailer;
use PHPUnit\Framework\TestCase;

class AcceptReservationTest extends TestCase {
  private AuthenticatedUser $tenant;
  private AuthenticatedUser $owner;

  private FixedAuthenticatedUserProvider $userProvider;
  private RamReservationRepository $reservationRepository;
  private RamHouseRepository $houseRepository;
  private RamUserRepository $userRepository;
  private RamMailer $mailer;

  private AcceptReservationCommandHandler $commandHandler;

  protected function setUp(): void {
    $this->tenant = new AuthenticatedUser("tenant-id");
    $this->owner = new AuthenticatedUser("owner-id");

    $reservation = new Reservation(
      "reservation-id",
      "house-id",
      "tenant-id",
      new \DateTime("2022-01-01"),
      new \DateTime("2022-01-02"),
      ReservationStatus::PENDING
    );

    $house = new House("house-id", "owner-id");
    $house->addReservation($reservation);

    $this->userProvider = new FixedAuthenticatedUserProvider($this->owner);
    $this->reservationRepository = new RamReservationRepository([$reservation]);
    $this->houseRepository = new RamHouseRepository([$house]);
    $this->userRepository = new RamUserRepository([
      User::create("tenant-id", "tenant@gmail.com", "azerty"),
      User::create("owner-id", "owner@gmail.com", "azerty")
    ]);
    $this->mailer = new RamMailer();

    $this->commandHandler = new AcceptReservationCommandHandler(
      $this->reservationRepository,
      $this->houseRepository,
      $this->userProvider,
      $this->userRepository,
      $this->mailer
    );
  }

  public function test_happyPath_shouldAcceptTheReservation() {
    $command = new AcceptReservationCommand("reservation-id");
    ($this->commandHandler)($command);

    $reservation = $this->reservationRepository->findById("reservation-id");
    $this->assertEquals(ReservationStatus::ACCEPTED, $reservation->getStatus());
  }

  public function test_happyPath_shouldUpdateTheCalendar() {
    $command = new AcceptReservationCommand("reservation-id");
    ($this->commandHandler)($command);

    $house = $this->houseRepository->findById("house-id");
    $entry = $house->findEntryById("reservation-id");

    $this->assertEquals(EntryStatus::ACCEPTED, $entry->getStatus());
  }
}