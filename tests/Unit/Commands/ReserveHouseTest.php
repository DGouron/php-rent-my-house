<?php
namespace App\Tests\Unit\Commands;

use App\Application\Commands\ReserveHouse\ReserveHouseCommand;
use App\Application\Commands\ReserveHouse\ReserveHouseCommandHandler;
use App\Application\Ports\Services\IIdProvider;
use PHPUnit\Framework\TestCase;

class IdProvider implements IIdProvider {
    public function getId() {
        return "reservation-id";
    }
}

class ReserveHouseTest extends TestCase {
    public function test_happyPath_should_create_reservation() {
        $commandHandler = new ReserveHouseCommandHandler(new IdProvider());
        $commandHandler->execute(new ReserveHouseCommand("2024-01-01", "2024-01-02"));

        $reservation = $commandHandler->getReservation();

        $this->assertNotNull($reservation);
        $this->assertEquals("reservation-id", $reservation->getId());

        $this->assertEquals("2024-01-01", $reservation->getStartDate()->format("Y-m-d"));
        $this->assertEquals("2024-01-02", $reservation->getEndDate()->format("Y-m-d"));
    }
}