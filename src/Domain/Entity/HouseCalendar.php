<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class HouseCalendar {
  private string $id;

  private Collection $entries;

  public function __construct(string $id, array $entries = []) {
    $this->id = $id;
    $this->entries = new ArrayCollection();

    foreach ($entries as $entry) {
      $this->entries->add($entry);
    }
  }

  public function getId(): string {
    return $this->id;
  }

  public function findEntryById(string $id): ?CalendarEntry {
    foreach ($this->entries as $entry) {
      if ($entry->getId() === $id) {
        return $entry;
      }
    }

    return null;
  }

  public function addReservation(Reservation $reservation) {
    $this->entries->add(
      new CalendarEntry(
        $reservation->getId(),
        $reservation->getStartDate(),
        $reservation->getEndDate(),
        EntryStatus::PENDING,
        $this,
      )
    );
  }

  public function getEntries(): Collection {
    return $this->entries;
  }

  public function deleteById(string $id) {
    foreach ($this->entries as $key => $entry) {
      if ($entry->getId() === $id) {
        $this->entries->remove($key);
        return;
      }
    }
  }

  public function isAvailable(\DateTime $startDate, \DateTime $endDate): bool {
    foreach ($this->entries as $entry) {
      if ($startDate < $entry->getEndDate() && $endDate > $entry->getStartDate()) {
        return false;
      }
    }

    return true;
  }
}