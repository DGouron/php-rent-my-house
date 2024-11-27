<?php

namespace App\Tests\Infrastructure;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationTestCase extends WebTestCase {
  protected static ?bool $initiated = false;
  protected static KernelBrowser $client;

  public static function initialize(): KernelBrowser {
    self::$client = parent::createClient();
    $container = self::getContainer();
    $kernel = self::getContainer()->get('kernel');

    $application = new Application($kernel);
    $application->setAutoExit(false);

    $entityManager = $container->get('doctrine')->getManager();
    $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
    $schemaTool = new SchemaTool($entityManager);
    $schemaTool->dropSchema($metadata);
    $schemaTool->createSchema($metadata);

    return self::$client;
  }
}