<?php

namespace App\Application\Commands\AcceptReservation;

use App\Application\Commands\ReserveHouse\ReserveHouseCommand;
use App\Application\Exception\ForbiddenException;
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

    if ($reservation === null) {
      throw new NotFoundException("Reservation not found");
    }

    $house = $this->houseRepository->findById($reservation->getHouseId());

    if ($house->getOwnerId() !== $this->userProvider->getUser()->getId()) {
      throw new ForbiddenException("Only the owner can accept a reservation");
    }

    $reservation->accept();

    $this->reservationRepository->save($reservation);

    $entry = $house->findEntryById($reservation->getId());
    $entry->accept();

    $this->houseRepository->save($house);

    $tenant = $this->userRepository->findById($reservation->getTenantId());

    $this->mailer->send((new Email())
      ->subject("Réservation acceptée")
      ->to($tenant->getEmailAddress())
      ->html("<p>Votre réservation a été acceptée</p>"));

    $owner = $this->userRepository->findById($house->getOwnerId());

    $this->mailer->send((new Email())
      ->subject("Confirmation d'acceptation")
      ->to($owner->getEmailAddress())
      ->html("<p>Votre acceptation a bien été pris en compte</p>")
    );
  }
}