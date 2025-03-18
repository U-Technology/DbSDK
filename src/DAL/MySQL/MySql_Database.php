<?php

namespace UTechnology\DbSDK\DAL\MySQL;

use Exception;
use PDO;
use PDOException;
use UTechnology\DbSDK\Config\ParameterDBMySQL;
use UTechnology\DbSDK\DAL\IDatabase;
use UTechnology\DbSDK\Enum\ParamTypeQuery;

class MySql_Database implements IDatabase
{
    private PDO $pdo;
    private static string $__connectionString = '';

    public function __construct(){
        try {
            if (self::$__connectionString == ''){
                self::$__connectionString = "mysql:host=" . ParameterDBMySQL::getHost() . ";dbname=" . ParameterDBMySQL::getDbName();
            }
            $this->pdo = new PDO(self::$__connectionString, ParameterDBMySQL::getUsername(), ParameterDBMySQL::getPassword());
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Could not connect to database. " . $e->getMessage());
        }
    }


    /**Execute select statement for query in input and returns first record founded
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function selectFirst(string $query = "", array $params = []): mixed
    {
        try {
            $list = $this->executeStatement( $query , $params );
            return $list[0] ?? null;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }

    /**Execute select statement for query in input
     * @param string $query Query for load data
     * @param array $params Parameters used in query
     * @return array Array of object loaded from DB
     * @throws Exception
     */
    public function select(string $query = "" , array $params = []): array
    {
        try {
            return $this->executeStatement( $query , $params );
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }

    /**Execute statement without returns data
     * @param string $query Query to execute
     * @param array $params Parameters used in query
     * @return true If query is execute correctly, return True
     * @throws Exception
     */
    public function Execute(string $query = "", array $params = []): bool
    {
        try {
            $this->executeStatement( $query , $params , false);
            return true;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }

    private function executeStatement($query = "" , $params = [], $returnValue = true)
    {
        try {
            $sth = $this->pdo->prepare($query);
            foreach ($params as $itemPar) {
                $sth->bindParam(":" . $itemPar->Name, $itemPar->Value, $this->GetParamType($itemPar->ParamType));
            }

            $result = $sth->execute();

            if (!$result) {
                throw new Exception('Errore esecuzione query: ' . implode(',',  $this->pdo->errorInfo()));
            }


            if ($returnValue){
                return $sth->fetchAll();
            }
            else {
                return true;
            }
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }

    private function GetParamType(ParamTypeQuery $type): int{
        return match ($type) {
            ParamTypeQuery::Boolean => PDO::PARAM_BOOL,
            ParamTypeQuery::Integer => PDO::PARAM_INT,
            default => PDO::PARAM_STR,
        };

    }

    /**Execute statement to save modified data
     * @param string $query Query to execute
     * @param array $params Params used in query
     * @return bool If query is execute correctly, return True
     * @throws Exception
     */
    public function Save(string $query = "", array $params = []): bool
    {
        try{
            return $this->executeStatement($query, $params, false);
        }catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }

    /**Execute statement to delete data
     * @param string $query Query to execute
     * @param array $params Params used in query
     * @return bool If query is execute correctly, return True
     * @throws Exception
     */
    public function Delete(string $query = "", array $params = []): bool
    {
        try{
            return $this->executeStatement($query, $params, false);
        }catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }
}