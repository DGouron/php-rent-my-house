<?php

namespace App\Tests\Fixtures;

use App\Application\Ports\Repositories\IHouseCalendarRepository;
use App\Domain\Entity\HouseCalendar;
use App\Tests\Infrastructure\IFixture;
use Symfony\Component\DependencyInjection\Container;

class HouseCalendarFixture implements IFixture {
  public function __construct(private readonly HouseCalendar $houseCalendar) {
  }

  /**
   * @return HouseCalendar
   */
  public function getHouseCalendar(): HouseCalendar {
    return $this->houseCalendar;
  }

  public function load(Container $container): void {
    //** @var IHouseCalendarRepository $houseRepository */
    $houseCalendarRepository = $container->get(IHouseCalendarRepository::class);
    $houseCalendarRepository->save($this->houseCalendar);
  }
}