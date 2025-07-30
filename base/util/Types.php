<?php

namespace Nnk2\Base\Util;

require_once __DIR__ . '/autoload.php';

/**
 * データ型の列挙体
 */
enum Types {
    case NULL;
    case BOOL;
    case INT;
    case FLOAT;
    case STRING;
    case ARRAY;
    case ENUM;
    case DATETIME;
    case OBJECT;
    case MODEL;
}
