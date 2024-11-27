<?php

namespace App\Application\Commands\ReserveHouse;

use App\Application\Ports\Repositories\IReservationRepository;
use App\Application\Ports\Services\IIdProvider;
use App\Domain\Entity\Reservation;
use App\Domain\ViewModel\IdViewModel;
use DateTime;

class ReserveHouseCommandHandler {
  private IIdProvider $idProvider;
  private IReservationRepository $repository;

  public function __construct(IIdProvider $idProvider, IReservationRepository $repository) {
    $this->idProvider = $idProvider;
    $this->repository = $repository;
  }

  public function execute(ReserveHouseCommand $command) {
    $reservation = new Reservation(
      $this->idProvider->getId(),
      DateTime::createFromFormat("Y-m-d", $command->getStartDate()),
      DateTime::createFromFormat("Y-m-d", $command->getEndDate())
    );

    $this->repository->save($reservation);

    return new IdViewModel($reservation->getId());
  }
}