<?php

namespace Nnk2\Base\data\db;

require_once __DIR__ . '/autoload.php';

/**
 * DBエラーの列挙体
 */
enum DbErrors: int {
/** 処理成功  */
    case SUCCESS = 0;

/** 該当データなし */
    case NO_DATA = -1;

/** 接続失敗  */
    case ERR_CONNECT = -101;
/** トランザクション開始失敗  */
    case ERR_BGINTXN = -102;
/** SQLの文法エラー  */
    case ERR_STATEMENT = -103;
/** SQLへの値のバインド失敗  */
    case ERR_BIND = -104;
/** SQL文の実行失敗  */
    case ERR_EXECUTE = -105;
/** 結果の取得失敗  */
    case ERR_FETCH = -106;
/** トランザクション反映の失敗  */
    case ERR_COMMIT = -107;
/** トランザクション撤回の失敗  */
    case ERR_ROLLBACK = -108;
/** 接続断の失敗  */
    case ERR_CLOSE = -109;
/** PDO系の失敗  */
    case ERR_PDO = -110;
}
