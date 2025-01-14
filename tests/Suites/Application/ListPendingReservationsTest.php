<?php

namespace App\Tests\Suites\Application;

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

class ListPendingReservationsTest extends ApplicationTestCase {
  private UserFixture $tenant;

  private UserFixture $owner;

  private UserFixture $ownerWithoutReservations;

  public function setUp(): void {
    parent::setUp();

    self::initialize();

    $this->tenant = new UserFixture(
      User::create("tenant-id", "tenant@gmail.com", "azerty")
    );

    $this->owner = new UserFixture(
      User::create("owner-id", "owner@gmail.com", "azerty")
    );

    $this->ownerWithoutReservations = new UserFixture(
      User::create("owner-without-reservations-id", "owner-without-reservations@gmail.com", "azerty")
    );

    $house = new HouseFixture(new House("house-id", "owner-id"));

    $pendingReservation = new ReservationFixture(
      new Reservation(
        "pending-id",
        "house-id",
        "tenant-id",
        new \DateTime("2022-01-01"),
        new \DateTime("2022-01-02"),
        ReservationStatus::PENDING
      )
    );

    $acceptedReservation = new ReservationFixture(
      new Reservation(
        "accepted-id",
        "house-id",
        "tenant-id",
        new \DateTime("2022-01-03"),
        new \DateTime("2022-01-04"),
        ReservationStatus::ACCEPTED
      )
    );

    $refusedReservation = new ReservationFixture(
      new Reservation(
        "refused-id",
        "house-id",
        "tenant-id",
        new \DateTime("2022-01-05"),
        new \DateTime("2022-01-06"),
        ReservationStatus::REFUSED
      )
    );

    $houseCalendarFixture = new HouseCalendarFixture(new HouseCalendar("house-id", []));
    $houseCalendar = $houseCalendarFixture->getHouseCalendar();
    $houseCalendar->addReservation($pendingReservation->getReservation());
    $houseCalendar->addReservation($acceptedReservation->getReservation());

    $this->load([
      $this->tenant,
      $this->owner,
      $this->ownerWithoutReservations,
      $house,
      $houseCalendarFixture,
      $pendingReservation,
      $acceptedReservation,
      $refusedReservation
    ]);
  }

  public function test_asOwner_shouldShowReservations() {
    $this->owner->authenticate(self::$client);

    $this->request('GET', '/api/list-pending-reservations', []);

    $this->assertResponseStatusCodeSame(200);

    $response = self::$client->getResponse();
    $data = json_decode($response->getContent(), true);

    $this->assertEquals([
      [
        "id" => "pending-id",
        "startDate" => "2022-01-01 00:00:00",
        "endDate" => "2022-01-02 00:00:00",
        "house" => [
          "id" => "house-id"
        ],
        "tenant" => [
          "id" => "tenant-id",
          "emailAddress" => "tenant@gmail.com"
        ]
      ]
    ], $data);
  }

  public function test_asOwnerWithoutReservations_shouldSeeNothing() {
    $this->ownerWithoutReservations->authenticate(self::$client);

    $this->request('GET', '/api/list-pending-reservations', []);

    $this->assertResponseStatusCodeSame(200);

    $response = self::$client->getResponse();
    $data = json_decode($response->getContent(), true);

    $this->assertEquals([], $data);
  }
}