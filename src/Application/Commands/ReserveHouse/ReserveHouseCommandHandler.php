<?php

namespace App\Application\Commands\ReserveHouse;

use App\Application\Ports\Services\IIdProvider;
use App\Application\Ports\Repositories\IHouseRepository;
use App\Domain\Entity\Reservation;
use App\Application\Ports\Repositories\IReservationRepository;
use App\Domain\ViewModel\IdViewModel;
use DateTime;

class ReserveHouseCommandHandler {

    private IIdProvider $idProvider;
    private IReservationRepository $reservationRepository;
    private IHouseRepository $houseRepository;

    public function __construct(IIdProvider $idProvider, IReservationRepository $reservationRepository, IHouseRepository $houseRepository){
        $this->idProvider = $idProvider;    
        $this->reservationRepository = $reservationRepository;
        $this->houseRepository = $houseRepository;
    }

    public function execute(ReserveHouseCommand $command): IdViewModel {
        $house = $this->houseRepository->findById($command->getHouseId());
        if ($house === null) {
            throw new \Exception("House not found");
        }

        $reservation = new Reservation(
            $this->idProvider->getId(), 
            $command->getHouseId(),
            DateTime::createFromFormat("Y-m-d", $command->getStartDate()),
            DateTime::createFromFormat("Y-m-d", $command->getEndDate())
        );

        $this->reservationRepository->save($reservation);

        return new IdViewModel($reservation->getId());
    }
}