<?php
namespace App\Application\Controller;

use App\Application\Commands\AcceptReservation\AcceptReservationCommand;
use App\Application\Commands\ReserveHouse\ReserveHouseCommand;
use App\Lib\AppController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AppController {
  #[Route('/api/reserve-house', format: "json")]
  public function reserveHouse(#[MapRequestPayload] ReserveHouseCommand $command) {
    return $this->dispatch($command);
  }

  #[Route('/api/accept-reservation', format: "json")]
  public function acceptReservation(#[MapRequestPayload] AcceptReservationCommand $command) {
    return $this->dispatch($command);
  }
}