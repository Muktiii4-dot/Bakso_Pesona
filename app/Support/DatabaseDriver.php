<?php

namespace App\Support;

class DatabaseDriver
{
    public static function supportsMySqlFeatures(string $driver): bool
    {
        return in_array($driver, ['mysql', 'mariadb'], true);
    }
}
