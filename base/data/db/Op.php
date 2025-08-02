<?php

namespace nnk2\base\data\db;

require_once __DIR__ . '/autoload.php';

/**
 * 条件演算子の列挙体
 */
enum Op {
/** @var string No Operation 空白 */
    case NOP;
/** @var string EQual 等しい */
    case EQ;
/** @var string Not Equal 等しくない */
    case NE;
/** @var string Littler Than ～より小さい */
    case LT;
/** @var string Greater Than ～より大きい */
    case GT;
/** @var string Littler Equel ～以下 */
    case LE;
/** @var string Greater Equel ～以上 */
    case GE;
/** @var string IN リストにある */
    case IN;
/** @var string Not IN リストにない */
    case NOT_IN;
/** @var string Between AとBの間 */
    case BETWEEN;
/** @var string Starts with ～で始まる */
    case STARTS;
/** @var string Ends with ～で終わる */
    case ENDS;
/** @var string Contains ～を含む */
    case CONTAINS;
/** @var string is null NULLである */
    case NULL;
/** @var string is not null NULLでない */
    case NOT_NULL;
/** @var string AND 両者が真となる */
    case AND;
/** @var string OR どちらかが真となる */
    case OR;

    /**
     * 必要なパラメータの数を返す
     * @param Op $op 比較演算子
     * @return int パラメータ数
     */
    public static function param(Op $op): int {
        switch ($op) {
            case self::NULL:
            case self::NOT_NULL:
            case self::AND:
            case self::OR:
            case self::NOP:
                return 0; // これらはパラメータを必要としない
            case self::BETWEEN:
                return 2; // BETWEENは2つの値が必要
            default:
                return 1; // 他の演算子は1つの値が必要
        }
    }
}
