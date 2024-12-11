<?php

namespace App\Application\Commands\ReserveHouse;

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
class ReserveHouseCommandHandler {
  private IIdProvider $idProvider;
  private IReservationRepository $repository;
  private IHouseRepository $houseRepository;
  private IUserProvider $userProvider;
  private IUserRepository $userRepository;
  private IMailer $mailer;

  public function __construct(
    IIdProvider $idProvider,
    IReservationRepository $repository,
    IHouseRepository $houseRepository,
    IUserProvider $userProvider,
    IUserRepository $userRepository,
    IMailer $mailer
  ) {
    $this->idProvider = $idProvider;
    $this->repository = $repository;
    $this->houseRepository = $houseRepository;
    $this->userProvider = $userProvider;
    $this->userRepository = $userRepository;
    $this->mailer = $mailer;
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

    $house->addReservation($reservation);
    $this->houseRepository->save($house);

    $owner = $this->userRepository->findById($house->getOwnerId());

    $this->mailer->send(
      (new Email())
        ->subject("Nouvelle réservation")
        ->to($owner->getEmailAddress())
        ->html("Une nouvelle réservation a été effectuée sur votre maison")
    );

    return new IdViewModel($reservation->getId());
  }

  public function __invoke(ReserveHouseCommand $command) {
    return $this->execute($command);
  }
}