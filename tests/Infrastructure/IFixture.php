<?php

namespace App\Tests\Infrastructure;

use Symfony\Component\DependencyInjection\Container;

interface IFixture {
  public function load(Container $container): void;
}