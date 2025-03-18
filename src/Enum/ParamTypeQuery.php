<?php

namespace UTechnology\DbSDK\Enum;

enum ParamTypeQuery
{
    case String;
    case Boolean;
    case Integer;
    case Date;
    case Decimal;
    case DateTime;
}