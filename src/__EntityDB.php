<?php

namespace UTechnology\DbSDK;

use src\DAL\Database;
use src\ParameterQuery;

abstract class __EntityDB
{
    protected static array $__attributesMap = [];
    protected static array $__propertiesType = [];
    static array $attributeClass = [];

    protected static array $__selectQueries = [];
    protected static array $__selectParams = [];

    protected static array $__insertQueries = [];

    static array $__paramsInsert = [];

    protected static array $__updateQueries = [];

    static array $__paramsUpdate = [];
    protected static array $__deleteQueries = [];

    static array $__paramsDelete = [];

    protected static array $__autoIncrementsFields = [];
    protected static array $__primaryKeyFields = [];

    protected static array $__selectWithoutWhereQueries = [];
    public static function __getSelectWithoutWhereQuery() :string{
        self::createQuerySelect();
        return self::$__selectWithoutWhereQueries[static::class];
    }

    protected bool $__getLastID;
    protected bool $__isNewRecord = true;

    public function setIsNewRecord(bool $_isNewRecord): void
    {
        $this->__isNewRecord = $_isNewRecord;
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

    private function createSelectCommand($primaryKey): void
    {
        if (!isset(self::$__primaryKeyFields[static::class])) {
            return;
        }

        if (self::$attributeClass[self::$__attributeNameForTable] === '') {
            return;
        }

        if (!isset(self::$__selectQueries[static::class])){
            // create query for class
            self::createQuerySelect();
            $querySelect = self::$__selectWithoutWhereQueries[static::class];
            $querySelect = $querySelect . ' WHERE ' . self::$__primaryKeyFields[static::class] . ' = :' . self::$__primaryKeyFields[static::class];

            $parameter = new ParameterQuery();
            $parameter->Name = self::$__primaryKeyFields[static::class];
            $parameter->ParamType = self::$__propertiesType[self::$__primaryKeyFields[static::class]];
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
        if(isset(self::$__insertQueries[static::class])){
            return;
        }

        if (!isset(self::$__attributesMap[static::class])) {
            return;
        }

        $queryField = '';
        $queryParam = '';

        $paramsCollection = [];

        foreach (self::getAttribute() as $key => $val) {
            $addFieldInQuery = true;
            if (self::getAutoIncrementFields() === $key){
                $addFieldInQuery = false;
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
                $param->setParameterType(self::getPropertyType()[$key]);
                $paramsCollection = $param;
            }
        }

        self::$__paramsInsert[static::class] = $paramsCollection;

        $queryLastID = '';
        $this->__getLastID = false;
        if (self::getAutoIncrementFields() != '') {
            $queryLastID = ';
    SELECT LAST_INSERT_ID() AS ID';
            $this->__getLastID = true;
        }

        self::$__insertQueries[static::class] = 'INSERT INTO ' . self::$attributeClass[static::class][self::$__attributeNameForTable] . '
        (' . $queryField . ')
    VALUES
        (' . $queryParam . ')
    ' . $queryLastID;
    }

    private function createUpdateCommand(): void
    {
        if(isset(self::$__updateQueries[static::class])){
            return;
        }

        if(isset(self::$__attributesMap[static::class])){
            return;
        }

        if (!isset(self::$__primaryKeyFields[static::class])) {
            return;
        }

        $paramsCollection = [];

        $queryField = '';
        foreach (self::getAttribute() as $key => $val) {
            $isAutoincrement = false;
            if (self::getAutoIncrementFields() === $val) {
                $isAutoincrement = true;
            }
            if (!$isAutoincrement) {
                if ($queryField != '') {
                    $queryField = $queryField . ',';
                }
                $queryField = $queryField . $key . '= :' . $key;

                // create parameters array
                $param = new ParameterQuery();
                $param->Name = $key;
                $param->setParameterType(self::getPropertyType()[$key]);
                // add parameter to array
                $paramsCollection = $param;
            }
        }

        self::$__updateQueries[static::class] = 'UPDATE ' . self::$attributeClass[self::$__attributeNameForTable] . ' SET
    ' . $queryField . '
WHERE ' . self::getPrimaryKey() . ' = :' . self::getPrimaryKey();


        // create parameters array
        $param = new ParameterQuery();
        $param->Name = self::getPrimaryKey();
        $param->setParameterType(self::getPropertyType()[self::getPrimaryKey()]);
        // add parameter to array
        $paramsCollection = $param;

        self::$__paramsUpdate[static::class] = $paramsCollection;

    }

    private function createDeleteCommand(): void
    {
        if (isset(self::$__deleteQueries[static::class])){
            return;
        }

        if(isset(self::$__attributesMap[static::class])){
            return;
        }

        if (!isset(self::$__primaryKeyFields[static::class])) {
            return;
        }

        self::$__deleteQueries[static::class] = 'DELETE FROM ' . self::$attributeClass[self::$__attributeNameForTable] . ' WHERE ' . self::getPrimaryKey() . ' = :' . self::getPrimaryKey();

        // create parameters array
        $param = new ParameterQuery();
        $param->Name = self::getPrimaryKey();
        $param->setParameterType(self::getPropertyType()[self::getPrimaryKey()]);
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
                self::$attributeClass[static::class] = [self::$__attributeNameForTable => $instance->GetTableName()];
            }
        }

        $attributes = [];
        $types = [];

        // property attribute
        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                if ($instance instanceof \FieldName) {
                    $attribute[$instance->GetFieldName()] = $property->getName();
                    $types[$instance->GetFieldName()] = $property->getType();
                } else if ($instance instanceof \IsAutoIncrement) {
                    //  da sistemare ancora come array
                    self::$__autoIncrementsFields[static::class] = $property->getName();
                } else if ($instance instanceof \IsPrimaryKeyField) {
                    self::$__primaryKeyFields[static::class] = $property->getName();
                }
            }
        }

        self::$__attributesMap[static::class] = $attributes;
        self::$__propertiesType[static::class] = $types;
    }


    protected static function getPrimaryKey():string{
        return self::$__primaryKeyFields[static::class];
    }

    protected static function getPropertyType() :array{
        return self::$__propertiesType[static::class];
    }

    protected static function getAttribute():array{
        return self::$__attributesMap[static::class];
    }

    protected static function getAutoIncrementFields() :string{
        if (isset(self::$__autoIncrementsFields[static::class])) {
            return self::$__autoIncrementsFields[static::class];
        }
        return '';
    }


    /**Return insert command string
     * @return string
     */
    public function __getInsertCommand(): string
    {
        return self::$__insertQueries[static::class];
    }

    /**Return update command string
     * @return string
     */
    public function __getUpdateCommand(): string
    {
        return self::$__updateQueries[static::class];
    }

    /**Return delete command string
     * @return string
     */
    public function __getDeleteCommand(): string
    {
        return self::$__deleteQueries[static::class];
    }

    public function __getSelectCommand_singleRecord(): string{
        return self::$__selectQueries[static::class];
    }

    private function populateFromDB(array $data): void
    {
        foreach ($data as $field => $value) {
            if (isset(self::getAttribute()[$field])) {
                $propertyName = self::getAttribute()[$field];
                $this->$propertyName = $value;
            }
        }
    }

    protected static function getParamInsert() :array{
        return self::$__paramsInsert[static::class];
    }
    protected static function getParamUpdate() :array{
        return self::$__paramsUpdate[static::class];
    }

    protected static function getParamSelect() :ParameterQuery{
        return self::$__selectParams[static::class];
    }



    /* CRUD operation*/

    public function __Save()
    {
        // setting parameters DB with properties values of object
        $reflection = new \ReflectionClass($this);

        if ($this->__isNewRecord) {
            // is new record, execute insert statement
            foreach (self::getParamInsert() as $param) {
                $propertyName = self::getAttribute()[$param->Name];
                $property = $reflection->getProperty($propertyName);
                $param->Value = $property->getValue($this);
            }

            $db = new Database();

            if (!$this->__getLastID) {
                // no autoincrement field
                $db->Execute($this->__getInsertCommand(), self::getParamInsert());
            } else {
                // with autoincrement field
                $lastValue = $db->selectFirst(self::__getInsertCommand(), self::getParamInsert());
                $propertyID = $reflection->getProperty(self::getAutoIncrementFields());
                $propertyID->setValue($this, $lastValue);
            }

            $db = null;
        } else {
            // is an existing record, execute update statement
            foreach (self::getParamUpdate() as $param) {
                $propertyName = self::getAttribute()[$param->Name];
                $property = $reflection->getProperty($propertyName);
                $param->Value = $property->getValue($this);
            }

            $db = new Database();

            $db->Execute($this->__getUpdateCommand(), self::getParamUpdate());

            $db = null;
        }

    }

    private function __Load()
    {
        $db = new Database();

        $dbObject = $db->selectFirst(self::__getSelectCommand_singleRecord(), [self::getParamSelect()]);
        $this->populateFromDB($dbObject);

        $db = null;
    }
}