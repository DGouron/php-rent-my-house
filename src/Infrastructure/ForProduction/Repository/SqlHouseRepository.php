<?php

namespace App\Infrastructure\ForProduction\Repository;

use App\Application\Ports\Repositories\IHouseRepository;
use App\Domain\Entity\House;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SqlHouseRepository extends ServiceEntityRepository implements IHouseRepository {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, House::class);
  }

  public function findById(string $id): ?House {
    return $this->find($id);
  }

  public function save(House $house): void {
    $em = $this->getEntityManager();
    $em->persist($house);
  }
}