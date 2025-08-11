<?php
namespace App\Tests\Unit\Commands;

use App\Application\Commands\ReserveHouse\ReserveHouseCommand;
use App\Application\Commands\ReserveHouse\ReserveHouseCommandHandler;
use App\Application\Ports\Repositories\IHouseRepository;
use App\Application\Ports\Repositories\IReservationRepository;
use App\Application\Ports\Services\IIdProvider;
use App\Domain\Entity\House;
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

class HouseRepository implements IHouseRepository {
    private array $database = [];

    public function __construct(array $database = []) {
        $this->database = $database;
    }

    public function findById(string $id): ?House {
        foreach ($this->database as $house) {
            if ($house->getId() === $id) {
                return $house;
            }
        }
        return null;
    }
}


class ReserveHouseTest extends TestCase {
    private IdProvider $idProvider;
    private ReservationRepository $reservationRepository;
    private HouseRepository $houseRepository;
    private ReserveHouseCommandHandler $commandHandler;

    protected function setUp(): void {
        $this->idProvider = new IdProvider("reservation-id");
        $this->reservationRepository = new ReservationRepository();
        $this->houseRepository = new HouseRepository([new House("house-id")]);
        $this->commandHandler = new ReserveHouseCommandHandler($this->idProvider, $this->reservationRepository, $this->houseRepository);
    }
    public function test_happyPath_should_create_reservation() {
  
        $response =$this->commandHandler->execute(new ReserveHouseCommand("house-id", "2024-01-01", "2024-01-02"));

        $reservation = $this->reservationRepository->findById($response->getId());

        $this->assertNotNull($reservation);
        $this->assertEquals("house-id", $reservation->getHouseId());
        $this->assertEquals($response->getId(), $reservation->getId());

        $this->assertEquals("2024-01-01", $reservation->getStartDate()->format("Y-m-d"));
        $this->assertEquals("2024-01-02", $reservation->getEndDate()->format("Y-m-d"));
    }

    public function test_whenHouseNotDefined_shouldFail() {
        $this->expectException(\Exception::class);

        $command = new ReserveHouseCommand("not-found-id", "2024-01-01", "2024-01-02");
        $this->commandHandler->execute($command);
    }
}