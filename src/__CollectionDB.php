<?php

namespace UTechnology\DbSDK;

use ArrayObject;
use InvalidArgumentException;
use UTechnology\DbSDK\DAL\MySQL\Database;

abstract class __CollectionDB extends ArrayObject
{
    protected string $allowedType;

    public function __construct(string $allowedType, array $items = [])
    {
        $this->allowedType = $allowedType;

        $this->validateTypeDB();

        foreach ($items as $item) {
            $this->validateType($item);
        }

        parent::__construct($items, ArrayObject::ARRAY_AS_PROPS);
    }

    public function offsetSet($key, $value): void
    {
        $this->validateType($value);
        parent::offsetSet($key, $value);
    }

    protected function validateType(object $item): void
    {
        if (!$item instanceof $this->allowedType) {
            throw new InvalidArgumentException("Type of object not valid. Only {$this->allowedType} are valid.");
        }
    }

    protected function validateTypeDB():void{
        if (!is_subclass_of($this->allowedType, __EntityDB::class)){
            throw new InvalidArgumentException("{$this->allowedType} not is an entity DB.");
        }
    }

    public function add(object $item): void
    {
        $this->validateType($item);
        $this->append($item);
    }

    public function getType(): string
    {
        return $this->allowedType;
    }


    // Section for DB
    protected static array $__selectQueries = [];
    private static function __getQuerySelect(){
        if (isset(self::$__selectQueries[static::class])){
            return self::$__selectQueries[static::class];
        }
        return '';
    }

    protected static function __checkSqlSelect(): void
    {
        if (self::$allowedType::__getQuerySelectWithoutWhere() === ''){
            self::$__selectQueries[static::class] = (new self::$allowedType())::__getQuerySelectWithoutWhere();
            return;
        }
        self::$__selectQueries[static::class] = self::$allowedType::__getQuerySelectWithoutWhere();
    }

    public function __loadAll(): void
    {
        self::__checkSqlSelect();

        // load record from DB
        $db = new Database();

        $dbObject = $db->select(self::__getQuerySelect());
        //$this->populateFromDB($dbObject);
        foreach ($dbObject as &$record){
            $newObject = new self::$allowedType();
            $this->add($newObject->populateFromDB($record));
        }

        $db = null;
    }

    public function __loadWithWhere(string $whereQuery, ?array $params = null): void
    {
        self::__checkSqlSelect();

        $queryToLoad = self::__getQuerySelect() . " WHERE " . $whereQuery;

        // load record from DB
        $db = new Database();

        if (isset($params)){
            $dbObject = $db->select($queryToLoad, $params);
        }
        else{
            $dbObject = $db->select($queryToLoad);
        }

        foreach ($dbObject as &$record){
            $newObject = new self::$allowedType();
            $this->add($newObject->populateFromDB($record));
        }

        $db = null;
    }

    public function __saveAll(): void
    {
        $db = new Database();

        foreach ($this as &$item) {
            if (is_subclass_of($item::class, __EntityDB::class)){
                $item->__SavePrivate($db);
            }
        }
        
        $db = null;
    }
}