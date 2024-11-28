<?php

namespace App\Application\Commands\ReserveHouse;

use App\Application\Exception\NotFoundException;
use App\Application\Ports\Repositories\IHouseRepository;
use App\Application\Ports\Repositories\IReservationRepository;
use App\Application\Ports\Services\IIdProvider;
use App\Application\Ports\Services\IUserProvider;
use App\Domain\Entity\Reservation;
use App\Domain\ViewModel\IdViewModel;
use DateTime;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ReserveHouseCommandHandler {
  private IIdProvider $idProvider;
  private IReservationRepository $repository;
  private IHouseRepository $houseRepository;
  private IUserProvider $userProvider;

  public function __construct(IIdProvider $idProvider, IReservationRepository $repository, IHouseRepository $houseRepository, IUserProvider $userProvider) {
    $this->idProvider = $idProvider;
    $this->repository = $repository;
    $this->houseRepository = $houseRepository;
    $this->userProvider = $userProvider;
  }

  public function execute(ReserveHouseCommand $command) {
    $house = $this->houseRepository->findById($command->getHouseId());
    if (!$house) {
      throw new NotFoundException("House not found");
    }

    $reservation = new Reservation(
      $this->idProvider->getId(),
      $command->getHouseId(),
      $this->userProvider->getUser()->getId(),
      DateTime::createFromFormat("Y-m-d", $command->getStartDate()),
      DateTime::createFromFormat("Y-m-d", $command->getEndDate())
    );

    $this->repository->save($reservation);

    return new IdViewModel($reservation->getId());
  }

  public function __invoke(ReserveHouseCommand $command) {
    return $this->execute($command);
  }
}