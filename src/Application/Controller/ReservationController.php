<?php
namespace App\Application\Controller;

use App\Application\Commands\ReserveHouse\ReserveHouseCommand;
use App\Application\Commands\ReserveHouse\ReserveHouseCommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController {
  #[Route('/api/reserve-house', format: "json")]
  public function reserveHouse(ReserveHouseCommandHandler $commandHandler, EntityManagerInterface $entityManager, Request $request) {
    $requestBody = json_decode($request->getContent(), true);

    $command = new ReserveHouseCommand(
      $requestBody['houseId'],
      $requestBody['startDate'],
      $requestBody['endDate']
    );

    $response = $commandHandler->execute($command);
    $entityManager->flush();

    return $this->json($response);
  }
}