<?php

namespace src;

use src\Enum\ParamTypeQuery;

class ParameterQuery
{
    public string $Name;
    public $Value;
    public ParamTypeQuery $ParamType;

    public function setParameterType($typeValue): void
    {
        if (!$typeValue) {
            $this->ParamType = ParamTypeQuery::String;
        }

        $this->ParamType = match ($typeValue->getName()) {
            'int' => ParamTypeQuery::Integer,
            'bool' => ParamTypeQuery::Boolean,
            'float' => ParamTypeQuery::Decimal,
            default => ParamTypeQuery::String,
        };
    }
}