<?php

namespace Tests;

use Doctrine\DBAL\Connection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (in_array(DatabaseTransactions::class, class_uses_recursive(static::class))) {
            $this->beginDoctrineTransaction();
        }
    }

    protected function tearDown(): void
    {
        if (in_array(DatabaseTransactions::class, class_uses_recursive(static::class))) {
            $this->rollbackDoctrineTransaction();
        }

        parent::tearDown();
    }

    private function beginDoctrineTransaction(): void
    {
        $this->doctrineConnection()->beginTransaction();
    }

    private function rollbackDoctrineTransaction(): void
    {
        $conn = $this->doctrineConnection();

        while ($conn->isTransactionActive()) {
            $conn->rollBack();
        }

        app('em')->clear();
    }

    private function doctrineConnection(): Connection
    {
        return app('em')->getConnection();
    }
}
