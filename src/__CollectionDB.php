<?php

namespace UTechnology\DbSDK;

use ArrayObject;
use InvalidArgumentException;
use src\DAL\Database;

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
    static string $__sqlSelect = '';

    public function __loadAll(){
        if (self::$allowedType::__getQuerySelectWithoutWhere() === ''){
            self::$__sqlSelect = (new self::$allowedType())::__getQuerySelectWithoutWhere();
        }
        self::$__sqlSelect = self::$allowedType::__getQuerySelectWithoutWhere();

        // load record from DB
        $db = new Database();

        $dbObject = $db->select(self::$__sqlSelect);
        //$this->populateFromDB($dbObject);
        foreach ($dbObject as &$record){
            $newObject = new self::$allowedType();
            $this->add($newObject->populateFromDB($record));
        }

        $db = null;
    }
}