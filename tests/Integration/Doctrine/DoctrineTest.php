<?php

declare(strict_types = 1);

namespace Test\Integration\Doctrine;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Slim\App;

#[CoversNothing]
class DoctrineTest extends TestCase
{
    protected App $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = require __DIR__ . "/../../../bootstrap.php";
    }

    public function testEntityManagerInstanceExist(): void
    {
        /** @var EntityManager $em */
        $em = $this->app->getContainer()->get(EntityManager::class);
        $this->assertInstanceOf(EntityManager::class, $em);
    }

    public function testThereAreNoPreConnection(): void
    {
        /** @var EntityManager $em */
        $em = $this->app->getContainer()->get(EntityManager::class);
        $this->assertFalse($em->getConnection()->isConnected());
    }

    public function testConnectedAfterAction(): void
    {
        /** @var EntityManager $em */
        $em = $this->app->getContainer()->get(EntityManager::class);
        $result = $em->getConnection()->createQueryBuilder()
            ->select("1")
            ->executeQuery();

        $this->assertTrue($em->getConnection()->isConnected());
    }
}