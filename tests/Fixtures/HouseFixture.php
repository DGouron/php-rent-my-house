<?php

namespace App\Tests\Fixtures;

use App\Application\Ports\Repositories\IHouseRepository;
use App\Domain\Entity\House;
use App\Tests\Infrastructure\IFixture;
use Symfony\Component\DependencyInjection\Container;

class HouseFixture implements IFixture {
  public function __construct(private readonly House $house) {
  }

  public function load(Container $container): void {
    //** @var IHouseRepository $houseRepository */
    $houseRepository = $container->get(IHouseRepository::class);
    $houseRepository->save($this->house);
  }
}