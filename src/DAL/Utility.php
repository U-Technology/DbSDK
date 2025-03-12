<?php

namespace UTechnology\DbSDK\DAL;

use UTechnology\DbSDK\ConfigConnection;
use UTechnology\DbSDK\Enum\ConnectionType;

class Utility
{
    public static function createSqlSelect(string $tableName, string $sqlQueryWhere = '') :string{
        switch (ConfigConnection::getConnectionType()){
            case ConnectionType::MySQL:
                $query = 'SELECT * FROM ' . $tableName;
                if ($sqlQueryWhere != ''){
                    $query = $query . ' WHERE ' . $sqlQueryWhere;
                }

                return $query;
            case ConnectionType::PostgreSQL:
                throw new \Exception('To be implemented');
        }

        return '';
    }

    public static function createSqlInsert(string $tableName, string $fieldsList, string $parametersList, bool $getLastId, string $idFieldName = '') :string{
        switch (ConfigConnection::getConnectionType()){
            case ConnectionType::MySQL:
                $queryLastID = '';
                if ($getLastId && $idFieldName != ''){
                    $queryLastID = ';
    SELECT LAST_INSERT_ID() AS ID';
                }
                $query = 'INSERT INTO ' . $tableName . '
        (' . $fieldsList . ')
    VALUES
        (' . $parametersList . ')
    ' . $queryLastID;

                return $query;
            case ConnectionType::PostgreSQL:
                throw new \Exception('To be implemented');
        }

        return '';
    }
}