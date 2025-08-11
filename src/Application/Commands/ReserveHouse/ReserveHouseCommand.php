<?php

namespace App\Application\Commands\ReserveHouse;

class ReserveHouseCommand {
    public readonly string $startDate;
    public readonly string $endDate;

    public function __construct(string $startDate, string $endDate) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getStartDate(): string {
        return $this->startDate;
    }

    public function getEndDate(): string {
        return $this->endDate;
    }
}