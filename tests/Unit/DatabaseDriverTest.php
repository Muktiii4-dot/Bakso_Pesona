<?php

namespace Tests\Unit;

use App\Support\DatabaseDriver;
use PHPUnit\Framework\TestCase;

class DatabaseDriverTest extends TestCase
{
    public function test_mysql_and_mariadb_are_recognized_as_supporting_mysql_features(): void
    {
        $this->assertTrue(DatabaseDriver::supportsMySqlFeatures('mysql'));
        $this->assertTrue(DatabaseDriver::supportsMySqlFeatures('mariadb'));
        $this->assertFalse(DatabaseDriver::supportsMySqlFeatures('sqlite'));
    }
}
