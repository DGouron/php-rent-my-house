<?php

namespace App\Tests\Fixtures;

use App\Application\Ports\Repositories\IHouseRepository;
use App\Application\Ports\Repositories\IReservationRepository;
use App\Domain\Entity\House;
use App\Domain\Entity\Reservation;
use App\Tests\Infrastructure\IFixture;
use Symfony\Component\DependencyInjection\Container;

class ReservationFixture implements IFixture {
  public function __construct(private readonly Reservation $reservation) {
  }

  /**
   * @return Reservation
   */
  public function getReservation(): Reservation {
    return $this->reservation;
  }

  public function load(Container $container): void {
    //** @var IReservationRepository $reservationRepository */
    $reservationRepository = $container->get(IReservationRepository::class);
    $reservationRepository->save($this->reservation);
  }
}