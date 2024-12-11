<?php

namespace App\Tests\Suites\Application;

use App\Application\Ports\Repositories\IHouseCalendarRepository;
use App\Application\Ports\Repositories\IReservationRepository;
use App\Domain\Entity\EntryStatus;
use App\Domain\Entity\House;
use App\Domain\Entity\HouseCalendar;
use App\Domain\Entity\Reservation;
use App\Domain\Entity\ReservationStatus;
use App\Domain\Entity\User;
use App\Tests\Fixtures\HouseCalendarFixture;
use App\Tests\Fixtures\HouseFixture;
use App\Tests\Fixtures\ReservationFixture;
use App\Tests\Fixtures\UserFixture;
use App\Tests\Infrastructure\ApplicationTestCase;

class AcceptReservationTest extends ApplicationTestCase {
  private UserFixture $tenant;

  private UserFixture $owner;

  public function setUp(): void {
    parent::setUp();

    self::initialize();

    $this->tenant = new UserFixture(
      User::create("tenant-id", "tenant@gmail.com", "azerty")
    );

    $this->owner = new UserFixture(
      User::create("owner-id", "owner@gmail.com", "azerty")
    );

    $house = new HouseFixture(new House("house-id", "owner-id"));

    $reservation = new ReservationFixture(
      new Reservation(
        "reservation-id",
        "house-id",
        "tenant-id",
        new \DateTime("2022-01-01"),
        new \DateTime("2022-01-02"),
        ReservationStatus::PENDING
      )
    );

    $houseCalendar = new HouseCalendarFixture(new HouseCalendar("house-id", []));
    $houseCalendar->getHouseCalendar()->addReservation($reservation->getReservation());

    $this->load([
      $this->tenant,
      $this->owner,
      $house,
      $houseCalendar,
      $reservation,
    ]);
  }

  public function test_happyPath() {
    $this->owner->authenticate(self::$client);

    $this->request('POST', '/api/accept-reservation', [
      'reservationId' => 'reservation-id',
    ]);

    $this->assertResponseStatusCodeSame(200);

    /** @var IReservationRepository $reservationRepository */
    $reservationRepository = self::getContainer()->get(IReservationRepository::class);
    $reservation = $reservationRepository->findById("reservation-id");

    $this->assertNotNull($reservation);
    $this->assertEquals(ReservationStatus::ACCEPTED, $reservation->getStatus());

    /** @var IHouseCalendarRepository $houseCalendarRepository */
    $houseCalendarRepository = self::getContainer()->get(IHouseCalendarRepository::class);
    $house = $houseCalendarRepository->findById('house-id');
    $entry = $house->findEntryById("reservation-id");

    $this->assertNotNull($entry);
    $this->assertEquals(EntryStatus::ACCEPTED, $entry->getStatus());
  }

  public function test_requesterIsNotOwner_shouldFail() {
    $this->tenant->authenticate(self::$client);

    $this->request('POST', '/api/accept-reservation', [
      'reservationId' => 'reservation-id',
    ]);

    $this->assertResponseStatusCodeSame(403);

    /** @var IReservationRepository $reservationRepository */
    $reservationRepository = self::getContainer()->get(IReservationRepository::class);
    $reservation = $reservationRepository->findById("reservation-id");

    $this->assertNotNull($reservation);
    $this->assertEquals(ReservationStatus::PENDING, $reservation->getStatus());

    /** @var IHouseCalendarRepository $houseCalendarRepository */
    $houseCalendarRepository = self::getContainer()->get(IHouseCalendarRepository::class);
    $house = $houseCalendarRepository->findById('house-id');
    $entry = $house->findEntryById("reservation-id");

    $this->assertNotNull($entry);
    $this->assertEquals(EntryStatus::PENDING, $entry->getStatus());
  }
}