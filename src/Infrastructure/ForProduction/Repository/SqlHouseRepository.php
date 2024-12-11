<?php

namespace App\Infrastructure\ForProduction\Repository;

use App\Application\Ports\Repositories\IHouseRepository;
use App\Domain\Entity\House;
use App\Domain\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SqlHouseRepository extends ServiceEntityRepository implements IHouseRepository {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, House::class);
  }

  public function findById(string $id): ?House {
    return $this->hydrate($this->find($id));
  }

  public function save(House $house): void {
    $em = $this->getEntityManager();

    $house->setOwner($em->getReference(User::class, $house->getOwnerId()));

    $em->persist($house);
  }

  private function hydrate(?House $house): ?House {
    if ($house === null) {
      return null;
    }

    $house->setOwnerId($house->getOwner()->getId());

    return $house;
  }
}