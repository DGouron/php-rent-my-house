<?php

namespace App\Application\Commands\ReserveHouse;

use App\Application\Ports\Services\IIdProvider;
use App\Domain\Entity\Reservation;
use App\Application\Ports\Repositories\IReservationRepository;
use DateTime;

class ReserveHouseCommandHandler {

    private IIdProvider $idProvider;
    private IReservationRepository $reservationRepository;
    private Reservation $reservation;

    public function __construct(IIdProvider $idProvider, IReservationRepository $reservationRepository){
        $this->idProvider = $idProvider;    
        $this->reservationRepository = $reservationRepository;
    }

    public function execute(ReserveHouseCommand $command): string {
        $reservation = new Reservation(
            $this->idProvider->getId(), 
            DateTime::createFromFormat("Y-m-d", $command->getStartDate()),
            DateTime::createFromFormat("Y-m-d", $command->getEndDate())
        );

        $this->reservationRepository->save($reservation);

        return $reservation->getId();
    }
}