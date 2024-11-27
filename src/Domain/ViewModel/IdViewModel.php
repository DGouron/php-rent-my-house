<?php

namespace App\Domain\ViewModel;

class IdViewModel {
  private string $id;

  public function __construct(string $id) {
    $this->id = $id;
  }

  public function getId() : string {
    return $this->id;
  }
}