<?php

namespace App\Application\Commands\ReserveHouse;

use App\Application\Ports\Services\IIdProvider;
use App\Domain\Entity\Reservation;
use DateTime;

class ReserveHouseCommandHandler {

    private IIdProvider $idProvider;
    private Reservation $reservation;

    public function __construct(IIdProvider $idProvider){
        $this->idProvider = $idProvider;    
    }

    public function execute(ReserveHouseCommand $command): void {
        $this->reservation = new Reservation(
            $this->idProvider->getId(), 
            DateTime::createFromFormat("Y-m-d", $command->getStartDate()),
            DateTime::createFromFormat("Y-m-d", $command->getEndDate())
        );
    }

    public function getReservation(): Reservation {
        return $this->reservation;
    }
}