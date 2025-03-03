<?php

namespace Config;

class ParameterDBMySQL
{
    private static string $host = "";

    public static function getHost(): string
    {
        return self::$host;
    }

    public static function setHost(string $host): void
    {
        self::$host = $host;
    }


    private static string $dbName = "";

    public static function getDbName(): string
    {
        return self::$dbName;
    }

    public static function setDbName(string $dbName): void
    {
        self::$dbName = $dbName;
    }


    private static string $username = "";

    public static function getUsername(): string
    {
        return self::$username;
    }

    public static function setUsername(string $username): void
    {
        self::$username = $username;
    }


    private static string $password = "";

    public static function getPassword(): string
    {
        return self::$password;
    }

    public static function setPassword(string $password): void
    {
        self::$password = $password;
    }


}