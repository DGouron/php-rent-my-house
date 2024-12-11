<?php

namespace App\Tests\Suites\Unit\Commands;

use App\Application\Commands\AcceptReservation\AcceptReservationCommand;
use App\Application\Commands\AcceptReservation\AcceptReservationCommandHandler;
use App\Application\Commands\RefuseReservation\RefuseReservationCommand;
use App\Application\Commands\RefuseReservation\RefuseReservationCommandHandler;
use App\Application\Commands\ReserveHouse\ReserveHouseCommandHandler;
use App\Application\Exception\ForbiddenException;
use App\Application\Exception\NotFoundException;
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

class RefuseReservationTest extends TestCase {
  private AuthenticatedUser $tenant;
  private AuthenticatedUser $owner;

  private FixedAuthenticatedUserProvider $userProvider;
  private RamReservationRepository $reservationRepository;
  private RamHouseRepository $houseRepository;
  private RamUserRepository $userRepository;
  private RamMailer $mailer;

  private RefuseReservationCommandHandler $commandHandler;

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

    $this->commandHandler = new RefuseReservationCommandHandler(
      $this->reservationRepository,
      $this->houseRepository,
      $this->userProvider,
      $this->userRepository,
      $this->mailer
    );
  }

  public function test_happyPath_shouldRefuseTheReservation() {
    $command = new RefuseReservationCommand("reservation-id");
    ($this->commandHandler)($command);

    $reservation = $this->reservationRepository->findById("reservation-id");
    $this->assertEquals(ReservationStatus::REFUSED, $reservation->getStatus());
  }

  public function test_happyPath_shouldUpdateTheCalendar() {
    $command = new RefuseReservationCommand("reservation-id");
    ($this->commandHandler)($command);

    $house = $this->houseRepository->findById("house-id");
    $entry = $house->findEntryById("reservation-id");

    $this->assertNull($entry);
  }

  public function test_happyPath_shouldSendAnEmailToTheTenant() {
    $command = new RefuseReservationCommand("reservation-id");
    ($this->commandHandler)($command);

    $message = $this->mailer->inbox[0];

    $this->assertEquals("Réservation refusée", $message->getSubject());
    $this->assertEquals("tenant@gmail.com", $message->getTo()[0]->getAddress());
  }

  public function test_requesterIsNotOwner_shouldFail() {
    $this->expectException(ForbiddenException::class);

    $this->userProvider->setUser($this->tenant);

    $command = new RefuseReservationCommand("reservation-id");
    ($this->commandHandler)($command);
  }

  public function test_reservationNotFound_shouldFail() {
    $this->expectException(NotFoundException::class);

    $command = new RefuseReservationCommand("this-id-does-not-exist");
    ($this->commandHandler)($command);
  }
}