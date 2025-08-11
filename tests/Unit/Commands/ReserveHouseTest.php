<?php
namespace App\Tests\Unit\Commands;

use App\Application\Commands\ReserveHouse\ReserveHouseCommand;
use App\Application\Commands\ReserveHouse\ReserveHouseCommandHandler;
use App\Application\Ports\Repositories\IReservationRepository;
use App\Application\Ports\Services\IIdProvider;
use App\Domain\Entity\Reservation;
use PHPUnit\Framework\TestCase;

class IdProvider implements IIdProvider {
    private string $id;
    public function __construct(string $id) {
        $this->id = $id;
    }

    public function getId(): string {
        return $this->id;
    }
}

class ReservationRepository implements IReservationRepository {
    private ?Reservation $reservation = null;
    public function save(Reservation $reservation): Reservation {
        $this->reservation = $reservation;
        return $reservation;    
    }

    public function findById(string $id): ?Reservation {
        return $this->reservation;
    }
}
class ReserveHouseTest extends TestCase {
    public function test_happyPath_should_create_reservation() {
        $repository = new ReservationRepository();
        $idProvider = new IdProvider("reservation-id");

        $commandHandler = new ReserveHouseCommandHandler($idProvider, $repository);
        $response =$commandHandler->execute(new ReserveHouseCommand("2024-01-01", "2024-01-02"));

        $reservation = $repository->findById($response->getId());

        $this->assertNotNull($reservation);
        $this->assertEquals($response->getId(), $reservation->getId());

        $this->assertEquals("2024-01-01", $reservation->getStartDate()->format("Y-m-d"));
        $this->assertEquals("2024-01-02", $reservation->getEndDate()->format("Y-m-d"));
    }
}