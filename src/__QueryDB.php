<?php

namespace UTechnology\DbSDK;

class __QueryDB
{
    //public function __construct(){}

    /**Read data from query
     * @param string $sqlStmt
     * @param array|null $params
     * @return array|null
     */
    public static function __readData(string $sqlStmt, ?array $params = null): ?array
    {
        $db = ConfigConnection::CreateDBInstance();

        $dbObject = null;

        if (isset($params)) {
            $dbObject = $db->select($sqlStmt, $params);
        }
        else{
            $dbObject = $db->select($sqlStmt);
        }

        $db = null;

        return $dbObject;
    }

    /**Execute query instruction
     * @param string $sqlStmt
     * @param array|null $params
     * @return void
     */
    public static function __executeSQL(string $sqlStmt, ?array $params = null){
        $db = ConfigConnection::CreateDBInstance();

        if (isset($params)) {
             $db->Execute($sqlStmt, $params);
        }
        else{
            $db->Execute($sqlStmt);
        }

        $db = null;
    }
}