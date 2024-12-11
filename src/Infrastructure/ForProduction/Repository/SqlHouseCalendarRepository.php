<?php

namespace App\Infrastructure\ForProduction\Repository;

use App\Application\Ports\Repositories\IHouseCalendarRepository;
use App\Domain\Entity\HouseCalendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SqlHouseCalendarRepository extends ServiceEntityRepository implements IHouseCalendarRepository {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, HouseCalendar::class);
  }

  public function findById(string $id): ?HouseCalendar {
    return $this->find($id);
  }

  public function save(HouseCalendar $houseCalendar): void {
    $em = $this->getEntityManager();
    $em->persist($houseCalendar);
  }
}