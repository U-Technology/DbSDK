<?php

namespace UTechnology\DbSDK;

use src\DAL\Database;
use src\ParameterQuery;

abstract class __EntityDB
{
    //static array $attributeMap = []; // mapping attribute -> property
    protected static array $__attributesMap = [];
    //static array $__propertyType = [];
    protected static array $__propertiesType = [];
    static array $attributeClass = [];
    protected static array $__selectQueries = [];
    protected static array $__selectParams = [];
    static string $__queryInsert = '';
    /** @var ParameterQuery[] */
    static array $__paramsInsert = [];
    static string $__queryUpdate = '';
    /** @var ParameterQuery[] */
    static array $__paramsUpdate = [];
    static string $__queryDelete = '';
    /** @var ParameterQuery[] */
    static array $__paramsDelete = [];
    static string $__fieldAutoIncrement = '';
    static string $__fieldNamePrimaryKey = '';

    protected static array $__selectWithoutWhereQueries = [];
    public static function __getSelectWithoutWhereQuery() :string{
        self::createQuerySelect();
        return self::$__selectWithoutWhereQueries[static::class];
    }

    static bool $__getLastID;
    static bool $__isNewRecord = true;

    public static function setIsNewRecord(bool $_isNewRecord): void
    {
        self::$__isNewRecord = $_isNewRecord;
    }

    static string $__attributeNameForTable = 'TableName';

    public function __construct($primaryKey = null)
    {
        $this->initializeAttributeMap();

        // Generate SQL query, if aren't just created
        if (isset($primaryKey)) {
            $this->createSelectCommand($primaryKey);
        }
        $this->CreateInsertCommand();
        $this->createUpdateCommand();
        $this->createDeleteCommand();

        // load object
        if (isset($primaryKey)) {
            $this->__Load();
        }
    }

    private function createSelectCommand($primaryKey)
    {
        if (self::$__fieldNamePrimaryKey === '') {
            return;
        }

        if (self::$attributeClass[self::$__attributeNameForTable] === '') {
            return;
        }

        if (!isset(self::$__selectQueries[static::class])){
            // create query for class
            self::createQuerySelect();
            $querySelect = self::$__selectWithoutWhereQueries[static::class];
            $querySelect = $querySelect . ' WHERE ' . self::$__fieldNamePrimaryKey . ' = :' . self::$__fieldNamePrimaryKey;

            $parameter = new ParameterQuery();
            $parameter->Name = self::$__fieldNamePrimaryKey;
            $parameter->ParamType = self::$__propertyType[self::$__fieldNamePrimaryKey];
            $parameter->Value = $primaryKey;

            self::$__selectParams[static::class] = $parameter;
        }
    }

    private static function createQuerySelect(): void
    {
        if (!isset(self::$__selectWithoutWhereQueries[static::class])){
            self::$__selectWithoutWhereQueries[static::class] = 'SELECT * FROM ' . self::$attributeClass[self::$__attributeNameForTable];
        }
    }

    private function CreateInsertCommand(): void
    {
        if (self::$__queryInsert != '') {
            return;
        }

        if (array_count_values(self::$attributeMap) == 0) {
            return;
        }

        $queryField = '';
        $queryParam = '';

        foreach (self::$attributeMap as $key => $val) {
            $addFieldInQuery = true;
            if (self::$__fieldAutoIncrement !== '') {
                if (self::$__fieldAutoIncrement === $val) {
                    $addFieldInQuery = false;
                }
            }
            if ($addFieldInQuery) {
                if ($queryField != '') {
                    $queryField = $queryField . ',';
                    $queryParam = $queryParam . ',';
                }
                $queryField = $queryField . $key;
                $queryParam = $queryParam . ':' . $key;

                // create parameters array
                $param = new ParameterQuery();
                $param->Name = $key;
                $param->setParameterType(self::$__propertyType[$key]);
                // add parameter to array
                self::$__paramsInsert[] = $param;
            }
        }

        $queryLastID = '';
        self::$__getLastID = false;
        if (self::$__fieldAutoIncrement !== '') {
            $queryLastID = ';
    SELECT LAST_INSERT_ID() AS ID';
            self::$__getLastID = true;
        }

        self::$__queryInsert = 'INSERT INTO ' . self::$attributeClass[self::$__attributeNameForTable] . '
        (' . $queryField . ')
    VALUES
        (' . $queryParam . ')
    ' . $queryLastID;
    }

    private function createUpdateCommand(): void
    {
        if (self::$__queryUpdate != '') {
            return;
        }

        if (array_count_values(self::$attributeMap) == 0) {
            return;
        }

        if (self::$__fieldNamePrimaryKey === '') {
            return;
        }

        $queryField = '';
        foreach (self::$attributeMap as $key => $val) {
            $isAutoincrement = false;
            if (self::$__fieldAutoIncrement !== '') {
                if (self::$__fieldAutoIncrement === $val) {
                    $isAutoincrement = true;
                }
            }
            if (!$isAutoincrement) {
                if ($queryField != '') {
                    $queryField = $queryField . ',';
                }
                $queryField = $queryField . $key . '= :' . $key;

                // create parameters array
                $param = new ParameterQuery();
                $param->Name = $key;
                $param->setParameterType(self::$__propertyType[$key]);
                // add parameter to array
                self::$__paramsUpdate[] = $param;
            }
        }

        self::$__queryUpdate = 'UPDATE ' . self::$attributeClass[self::$__attributeNameForTable] . ' SET
    ' . $queryField . '
WHERE ' . self::$__fieldNamePrimaryKey . ' = :' . self::$__fieldNamePrimaryKey;

        // create parameters array
        $param = new ParameterQuery();
        $param->Name = self::$__fieldNamePrimaryKey;
        $param->setParameterType(self::$__propertyType[self::$__fieldNamePrimaryKey]);
        // add parameter to array
        self::$__paramsUpdate[] = $param;

    }

    private function createDeleteCommand(): void
    {
        if (self::$__queryDelete != '') {
            return;
        }

        if (array_count_values(self::$attributeMap) == 0) {
            return;
        }

        if (self::$__fieldNamePrimaryKey === '') {
            return;
        }

        self::$__queryDelete = 'DELETE FROM ' . self::$attributeClass[self::$__attributeNameForTable] . ' WHERE ' . self::$__fieldNamePrimaryKey . ' = :' . self::$__fieldNamePrimaryKey;

        // create parameters array
        $param = new ParameterQuery();
        $param->Name = self::$__fieldNamePrimaryKey;
        $param->setParameterType(self::$__propertyType[self::$__fieldNamePrimaryKey]);
        // add parameter to array
        self::$__paramsDelete[] = $param;
    }

    private function initializeAttributeMap(): void
    {
        if (isset(self::$__attributesMap[static::class])){
            if (array_count_values(self::$__attributesMap[static::class]) != 0) {
                return;
            }
        }

        $reflection = new \ReflectionClass($this);

        // class attribute
        foreach ($reflection->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof \TableName) {
                self::$attributeClass[self::$__attributeNameForTable] = $instance->GetTableName();
            }
        }


        // property attribute
        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                if ($instance instanceof \FieldName) {
                    self::$__attributesMap[static::class] = [$instance->GetFieldName() => $property->getName()];
                    //self::$__propertyType[$instance->GetFieldName()] = $property->getType();
                    self::$__propertiesType[static::class] = [$instance->GetFieldName() => $property->getType()];
                } else if ($instance instanceof \IsAutoIncrement) {
                    self::$__fieldAutoIncrement = $property->getName();
                } else if ($instance instanceof \IsPrimaryKeyField) {
                    self::$__fieldNamePrimaryKey = $property->getName();
                }
            }
        }
    }




    /**Return insert command string
     * @return string
     */
    public function __getInsertCommand(): string
    {
        return self::$__queryInsert;
    }

    /**Return update command string
     * @return string
     */
    public function __getUpdateCommand(): string
    {
        return self::$__queryUpdate;
    }

    /**Return delete command string
     * @return string
     */
    public function __getDeleteCommand(): string
    {
        return self::$__queryDelete;
    }

    private function populateFromDB(array $data): void
    {
        foreach ($data as $field => $value) {
            if (isset($this->attributeMap[$field])) {
                $propertyName = $this->attributeMap[$field];
                $this->$propertyName = $value;
            }
        }
    }


    /* CRUD operation*/

    public function __Save()
    {
        // setting parameters DB with properties values of object
        $reflection = new \ReflectionClass($this);

        if (self::$__isNewRecord) {
            // is new record, execute insert statement
            foreach (self::$__paramsInsert as $param) {
                $propertyName = self::$attributeMap[$param->Name];
                $property = $reflection->getProperty($propertyName);
                $param->Value = $property->getValue($this);
            }

            $db = new Database();

            if (!self::$__getLastID) {
                // no autoincrement field
                $db->Execute(self::$__queryInsert, self::$__paramsInsert);
            } else {
                // with autoincrement field
                $lastValue = $db->selectFirst(self::$__queryInsert, self::$__paramsInsert);
                $propertyID = $reflection->getProperty(self::$__fieldAutoIncrement);
                $propertyID->setValue($this, $lastValue);
            }

            $db = null;
        } else {
            // is an existing record, execute update statement
            foreach (self::$__paramsUpdate as $param) {
                $propertyName = self::$attributeMap[$param->Name];
                $property = $reflection->getProperty($propertyName);
                $param->Value = $property->getValue($this);
            }

            $db = new Database();

            $db->Execute(self::$__queryUpdate, self::$__paramsUpdate);

            $db = null;
        }

    }

    private function __Load()
    {
        $db = new Database();

        $dbObject = $db->selectFirst(self::$__querySelect, [self::$__paramSelect]);
        $this->populateFromDB($dbObject);

        $db = null;
    }
}