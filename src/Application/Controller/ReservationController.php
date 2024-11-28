<?php
namespace App\Application\Controller;

use App\Application\Commands\ReserveHouse\ReserveHouseCommand;
use App\Application\Commands\ReserveHouse\ReserveHouseCommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController {
  #[Route('/api/reserve-house', format: "json")]
  public function reserveHouse(
    ReserveHouseCommandHandler $commandHandler,
    EntityManagerInterface $entityManager,
    #[MapRequestPayload] ReserveHouseCommand $command
  ) {
    $response = $commandHandler->execute($command);
    $entityManager->flush();

    return $this->json($response);
  }
}