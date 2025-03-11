<?php

namespace UTechnology\DbSDK\DAL;

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

    public function Save(string $query = "", array $params = []) :bool;

    public function Delete(string $query = "", array $params = []) :bool;
}