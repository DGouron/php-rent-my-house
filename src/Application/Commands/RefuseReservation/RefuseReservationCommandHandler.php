<?php

namespace App\Application\Commands\RefuseReservation;

use App\Application\Exception\ForbiddenException;
use App\Application\Exception\NotFoundException;
use App\Application\Ports\Repositories\IHouseCalendarRepository;
use App\Application\Ports\Repositories\IHouseRepository;
use App\Application\Ports\Repositories\IReservationRepository;
use App\Application\Ports\Repositories\IUserRepository;
use App\Application\Ports\Services\IMailer;
use App\Application\Ports\Services\IUserProvider;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class RefuseReservationCommandHandler {
  private IReservationRepository $reservationRepository;
  private IHouseRepository $houseRepository;
  private IUserProvider $userProvider;
  private IUserRepository $userRepository;
  private IHouseCalendarRepository $houseCalendarRepository;
  private IMailer $mailer;

  public function __construct(
    IReservationRepository   $reservationRepository,
    IHouseRepository         $houseRepository,
    IUserProvider            $userProvider,
    IUserRepository          $userRepository,
    IHouseCalendarRepository $houseCalendarRepository,
    IMailer                  $mailer
  ) {
    $this->reservationRepository = $reservationRepository;
    $this->houseRepository = $houseRepository;
    $this->userProvider = $userProvider;
    $this->userRepository = $userRepository;
    $this->houseCalendarRepository = $houseCalendarRepository;
    $this->mailer = $mailer;
  }

  public function __invoke(RefuseReservationCommand $command) {
    $reservation = $this->reservationRepository->findById($command->getReservationId());

    if ($reservation === null) {
      throw new NotFoundException("Reservation not found");
    }

    $house = $this->houseRepository->findById($reservation->getHouseId());

    if ($house->getOwnerId() !== $this->userProvider->getUser()->getId()) {
      throw new ForbiddenException("Only the owner can refuse a reservation");
    }

    $reservation->refuse();

    $this->reservationRepository->save($reservation);

    $houseCalendar = $this->houseCalendarRepository->findById($house->getId());
    $houseCalendar->deleteById($reservation->getId());
    $this->houseCalendarRepository->save($houseCalendar);

    $tenant = $this->userRepository->findById($reservation->getTenantId());
    $this->mailer->send((new Email())
      ->subject("Réservation refusée")
      ->to($tenant->getEmailAddress())
      ->html("<p>Votre réservation a été refusée</p>"));
  }
}