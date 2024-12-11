<?php

namespace App\Application\Commands\AcceptReservation;

use App\Application\Commands\ReserveHouse\ReserveHouseCommand;
use App\Application\Exception\NotFoundException;
use App\Application\Ports\Repositories\IHouseRepository;
use App\Application\Ports\Repositories\IReservationRepository;
use App\Application\Ports\Repositories\IUserRepository;
use App\Application\Ports\Services\IIdProvider;
use App\Application\Ports\Services\IMailer;
use App\Application\Ports\Services\IUserProvider;
use App\Application\ViewModel\IdViewModel;
use App\Domain\Entity\Reservation;
use DateTime;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class AcceptReservationCommandHandler {
  private IReservationRepository $reservationRepository;
  private IHouseRepository $houseRepository;
  private IUserProvider $userProvider;
  private IUserRepository $userRepository;
  private IMailer $mailer;

  public function __construct(
    IReservationRepository $reservationRepository,
    IHouseRepository       $houseRepository,
    IUserProvider          $userProvider,
    IUserRepository        $userRepository,
    IMailer                $mailer
  ) {
    $this->reservationRepository = $reservationRepository;
    $this->houseRepository = $houseRepository;
    $this->userProvider = $userProvider;
    $this->userRepository = $userRepository;
    $this->mailer = $mailer;
  }

  public function __invoke(AcceptReservationCommand $command) {
    $reservation = $this->reservationRepository->findById($command->getReservationId());
    $reservation->accept();

    $this->reservationRepository->save($reservation);

    $house = $this->houseRepository->findById($reservation->getHouseId());
    $entry = $house->findEntryById($reservation->getId());

    $entry->accept();

    $this->houseRepository->save($house);
  }
}