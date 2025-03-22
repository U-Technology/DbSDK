<?php

namespace UTechnology\DbSDK\DAL;

use Exception;

interface IDatabase
{
    /**Execute select statement for query in input and returns first record founded
     * @param string $query
     * @param array $params
     * @return mixed
     */
    public function selectFirst(string $query = "", array $params = []) :mixed;

    /**Execute select statement for query in input
     * @param string $query Query for load data
     * @param array $params Parameters used in query
     * @return array Array of object loaded from DB
     */
    public function select(string $query = "" , array $params = []) :array;

    /**Execute statement without returns data
     * @param string $query Query to execute
     * @param array $params Parameters used in query
     * @return true If query is execute correctly, return True
     */
    public function Execute(string $query = "", array $params = []): bool;

    /**Execute statement to save modified data
     * @param string $query Query to execute
     * @param array $params Params used in query
     * @return bool If query is execute correctly, return True
     * @throws Exception
     */
    public function Save(string $query = "", array $params = [], bool $getLastID = false) :bool;

    /**Execute statement to delete data
     * @param string $query Query to execute
     * @param array $params Params used in query
     * @return bool If query is execute correctly, return True
     */
    public function Delete(string $query = "", array $params = []) :bool;
}