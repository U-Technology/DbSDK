<?php

namespace UTechnology\DbSDK;

use src\Config\ParameterDBMySQL;
use UTechnology\DbSDK\Enum\ConnectionType;

class ConfigConnection
{
    protected static ConnectionType $__connectionType;
    public static function getConnectionType(): ConnectionType
    {
        return self::$__connectionType;
    }

    public static function settingMySqlConnection(string $host, string $dbName, string $userName, string $pwd): void
    {
        ParameterDBMySQL::setHost($host);
        ParameterDBMySQL::setDbName($dbName);
        ParameterDBMySQL::setUsername($userName);
        ParameterDBMySQL::setPassword($pwd);

        self::$__connectionType = ConnectionType::MySQL;
    }


}