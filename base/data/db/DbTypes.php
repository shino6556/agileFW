<?php

namespace Nnk2\Base\data\db;

require_once __DIR__ . '/autoload.php';

/**
 * DBMS上でのデータ型  
 * (バイト数はMariaDB)
 */
enum DbTypes {
	case NULL;
	case BOOL;
	case TINY_INT;  // 1byte
	case SMALL_INT; // 2byte
	case INT;       // 4byte
	case BIG_INT;   // 8byte
	case FLOAT;     // 4byte
	case DOUBLE;    // 8byte
	case NUMERIC;   // 9桁毎に4byte+剰余桁0-4byte
	case VARCHAR;   // 1文字3byte+文字数1-2byte
	case DATE;      // 3byte
	case TIMESTAMP; // 4byte
}
