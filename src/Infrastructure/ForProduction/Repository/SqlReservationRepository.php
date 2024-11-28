<?php

namespace App\Infrastructure\ForProduction\Repository;

use App\Application\Ports\Repositories\IReservationRepository;
use App\Domain\Entity\House;
use App\Domain\Entity\Reservation;
use App\Domain\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SqlReservationRepository extends ServiceEntityRepository implements IReservationRepository {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, Reservation::class);
  }

  public function findById(string $id): ?Reservation {
    return $this->hydrate($this->find($id));
  }

  public function save(Reservation $reservation): void {
    $em = $this->getEntityManager();

    $house = $em->getReference(House::class, $reservation->getHouseId());
    $reservation->setHouse($house);

    $tenant = $em->getReference(User::class, $reservation->getTenantId());
    $reservation->setTenant($tenant);

    $em->persist($reservation);
  }

  private function hydrate(?Reservation $reservation): ?Reservation {
    if ($reservation === null) {
      return null;
    }

    $reservation->setHouseId($reservation->getHouse()->getId());
    $reservation->setTenantId($reservation->getTenant()->getId());

    return $reservation;
  }
}