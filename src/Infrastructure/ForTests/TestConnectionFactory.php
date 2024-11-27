<?php

namespace App\Infrastructure\ForTests;

use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Tools\DsnParser;
use Testcontainers\Container\PostgresContainer;

class TestConnectionFactory extends ConnectionFactory {
  static $testDsn;

  public function __construct(array $typesConfig, ?DsnParser $dsnParser = null) {
    if (!$this::$testDsn) {
      $container = PostgresContainer::make('16', 'azerty')
        ->withPostgresDatabase('app_test')
        ->withPostgresUser('user')
        ->withPort(9870, 5432)
        ->run();

      $this::$testDsn = sprintf('postgresql://user:azerty@localhost:9870/app?serverVersion=16&charset=utf8');
    }
    parent::__construct($typesConfig, $dsnParser);
  }


  /**
   * @throws Exception
   */
  public function createConnection(array $params, ?Configuration $config = null, ?EventManager $eventManager = null, array $mappingTypes = []): \Doctrine\DBAL\Connection {
    $params['url'] = $this::$testDsn;
    return parent::createConnection($params, $config, $eventManager, $mappingTypes);
  }
}