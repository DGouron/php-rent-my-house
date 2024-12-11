<?php

namespace App\Infrastructure\ForProduction\Repository;

use App\Application\Ports\Repositories\IUserRepository;
use App\Domain\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SqlUserRepository extends ServiceEntityRepository implements IUserRepository {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, User::class);
  }

  public function findById(string $id): ?User {
    return $this->find($id);
  }

  public function save(User $user) {
    $this->getEntityManager()->persist($user);
  }
}