<?php

abstract class __EntityDB
{
    static array $attributeMap = []; // mapping attribute -> property
    static array $attributeClass = [];
    static string $__queryInsert = '';
    static string $__queryUpdate = '';
    static string $__fieldAutoIncrement = '';

    static string $__attributeNameForTable = 'TableName';

    public function __construct()
    {
        $this->initializeAttributeMap();

        // Generate SQL query, if aren't just created
        $this->CreateInsertCommand();
    }

    private function CreateInsertCommand(): void{
        if (self::$__queryInsert != ''){
            return;
        }

        if(array_count_values(self::$attributeMap) == 0){
            return;
        }

        $queryField = '';
        $queryParam = '';

        foreach (self::$attributeMap as $key => $val) {
            $addFieldInQuery = true;
            if (self::$__fieldAutoIncrement !== ''){
                if (self::$__fieldAutoIncrement === $val){
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
            }
        }

        self::$__queryInsert = 'INSERT INTO ' . self::$attributeClass[self::$__attributeNameForTable] . '
        (' . $queryField . ')
    VALUES
        (' . $queryParam .')';
    }

    private function createUpdateCommand(): void{
        if (self::$__queryUpdate != ''){
            return;
        }

        if(array_count_values(self::$attributeMap) == 0){
            return;
        }

        $queryField = '';

    }

    private function initializeAttributeMap(): void {
        if (array_count_values(self::$attributeMap) != 0){
            return;
        }
        $reflection = new \ReflectionClass($this);

        // Attributi della classe
        foreach ($reflection->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof \TableName){
                self::$attributeClass[self::$__attributeNameForTable] = $instance->GetTableName();
            }

        }


        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                if ($instance instanceof \FieldName) {
                    self::$attributeMap[$instance->GetFieldName()] = $property->getName();
                }
                else if ($instance instanceof \IsAutoIncrement){
                    self::$__fieldAutoIncrement = $property->getName();
                }
            }
        }
    }
}