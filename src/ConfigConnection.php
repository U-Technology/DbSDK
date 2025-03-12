<?php

namespace UTechnology\DbSDK;

use src\Config\ParameterDBMySQL;
use UTechnology\DbSDK\Config\ParameterDBPostgreSQL;
use UTechnology\DbSDK\DAL\IDatabase;
use UTechnology\DbSDK\DAL\MySQL\MySql_Database;
use UTechnology\DbSDK\DAL\PostgreSQL\PostGreSQL_Database;
use UTechnology\DbSDK\Enum\ConnectionType;

class ConfigConnection
{
    protected static ConnectionType $__connectionType;

    /**Get connection type to DB
     * @return ConnectionType
     */
    public static function getConnectionType(): ConnectionType
    {
        return self::$__connectionType;
    }

    /**Setting parameter connection for MySQL DB. This function settings automatically the connection type to MySQL.
     * @param string $host Server host for connection
     * @param string $dbName DbName to connect
     * @param string $userName UserName to use for connection
     * @param string $pwd Pwd to use for connection
     * @return void
     */
    public static function settingConnectionMySQL(string $host, string $dbName, string $userName, string $pwd): void
    {
        ParameterDBMySQL::setHost($host);
        ParameterDBMySQL::setDbName($dbName);
        ParameterDBMySQL::setUsername($userName);
        ParameterDBMySQL::setPassword($pwd);

        self::$__connectionType = ConnectionType::MySQL;
    }


    /**Setting parameter connection for PostgreSQL DB. This function settings automatically the connection type to PostgreSQL.
     * @param string $host Server host for connection
     * @param string $dbName DbName to connect
     * @param string $userName UserName to use for connection
     * @param string $pwd Pwd to use for connection
     * @return void
     */
    public static function settingConnectionPostgreSQL(string $host, string $dbName, string $userName, string $pwd): void
    {
        ParameterDBPostgreSQL::setHost($host);
        ParameterDBPostgreSQL::setDbName($dbName);
        ParameterDBPostgreSQL::setUsername($userName);
        ParameterDBPostgreSQL::setPassword($pwd);

        self::$__connectionType = ConnectionType::PostgreSQL;
    }

    /**Create instance DB for connect DataBase.
     * Checking the connection type and create instance correctly
     * @return IDatabase
     */
    public static function CreateDBInstance() :IDatabase{
        switch (self::$__connectionType){
            case ConnectionType::MySQL:
                return new MySql_Database();
            case ConnectionType::PostgreSQL:
                return new PostGreSQL_Database();
        }
    }
}