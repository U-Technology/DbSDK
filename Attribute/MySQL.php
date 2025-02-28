<?php
#[Attribute(Attribute::TARGET_CLASS)]
class TableName{
    private string $tableName;

    public function __construct($tableName = null)
    {
        $this->tableName = $tableName;
    }

    public function GetTableName() : string{
        return $this->tableName;
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class FieldName{
    private string $fieldName;

    public function __construct($fieldName = null)
    {
        $this->fieldName = $fieldName;
    }

    public function GetFieldName() : string{
        return $this->fieldName;
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class IsPrimaryKeyField{}

#[Attribute(Attribute::TARGET_PROPERTY)]
class IsAutoIncrement{}