<?php

namespace App\Application\Queries\ListPendingReservations;

use App\Application\Ports\Services\IUserProvider;
use App\Application\ViewModel\HouseViewModel;
use App\Application\ViewModel\PendingReservationViewModel;
use App\Application\ViewModel\TenantViewModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ListPendingReservationsQueryHandler {
  private EntityManagerInterface $entityManager;

  private IUserProvider $userProvider;

  public function __construct(EntityManagerInterface $entityManager, IUserProvider $userProvider) {
    $this->entityManager = $entityManager;
    $this->userProvider = $userProvider;
  }

  public function __invoke(ListPendingReservationsQuery $query) {
    $result = $this->entityManager->getConnection()->executeQuery('
      SELECT 
        r.id as r_id, r.start_date as r_start_date, r.end_date as r_end_date,
        u.id as u_id, u.email_address as u_email_address,
        h.id as h_id
      FROM reservations r
      LEFT JOIN users u ON r.tenant_id = u.id
      LEFT JOIN houses h ON r.house_id = h.id
      WHERE r.status = \'pending\'
      AND h.owner_id = :owner_id
    ', ["owner_id" => $this->userProvider->getUser()->getId()]);

    $output = [];

    while (($r = $result->fetchAssociative()) !== false) {
      $output[] = new PendingReservationViewModel(
        $r["r_id"],
        $r["r_start_date"],
        $r["r_end_date"],
        new HouseViewModel($r["h_id"]),
        new TenantViewModel($r["u_id"], $r["u_email_address"])
      );
    }

    return $output;
  }
}