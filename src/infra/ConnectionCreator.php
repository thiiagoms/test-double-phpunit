<?php

declare(strict_types=1);

namespace Thiiagoms\Infra;

use PDO;

class ConnectionCreator
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (is_null(self::$pdo)) {
            $caminhoBanco = __DIR__ . '/../../banco.sqlite';
            self::$pdo = new \PDO('sqlite:' . $caminhoBanco);
            self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }
}