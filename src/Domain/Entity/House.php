<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class House {
  private string $id;

  private Collection $entries;

  private string $ownerId;

  private User $owner;

  public function __construct(string $id, string $ownerId, array $entries = []) {
    $this->id = $id;
    $this->ownerId = $ownerId;
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

  /**
   * @return string
   */
  public function getOwnerId(): string {
    return $this->ownerId;
  }

  public function setOwnerId(string $ownerId): void {
    $this->ownerId = $ownerId;
  }

  public function getOwner(): User {
    return $this->owner;
  }

  public function setOwner(User $owner): void {
    $this->owner = $owner;
  }
}