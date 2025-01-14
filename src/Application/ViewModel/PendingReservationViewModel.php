<?php

namespace App\Application\ViewModel;

class HouseViewModel {
  public function __construct(public string $id) {
  }
}

class TenantViewModel {
  public function __construct(public string $id, public string $emailAddress) {
  }
}

class PendingReservationViewModel {
  public function __construct(
    public string          $id,
    public string          $startDate,
    public string          $endDate,
    public HouseViewModel  $house,
    public TenantViewModel $tenant
  ) {
  }
}