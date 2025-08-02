<?php

namespace nnk2\app\validate\field;

require_once __DIR__ . '/autoload.php';

use nnk2\base\util\Results;
use nnk2\base\util\StrUtil;
use nnk2\base\data\model\Model;
use nnk2\base\validate\field\ValField;

/**
 * 整数のバリデーション  
 * 半数字とマイナス符号のみを許可する。
 */
class ValInt extends ValField {
    public function __construct() {
    }
    /**
     * バリデーションのインスタンスを取得する
     * @param Model $model 検証対象のモデル
     * @param ?array $row 検証対象の配列 
     * @param string $fieldName フィールド名
     * @param Results $results 検証結果保持
     * @return ValInt 検証インスタンス
     */
    public static function start(Model $model, ?array $row, string $fieldName, Results $results): ValInt {
        return self::startBase(self::$all, $model, $row, $fieldName, $results);
    }
    private static array $all = [];

    /**
     * @inheritDoc
     * mandatory,checkHan,checkStr,checkInt の順に呼び出す
     */
    public function check(bool $mandatory = false, int|string ...$arg): ValInt {
        $this->mandatory($mandatory)->checkHan()->checkStr();
        return $this->checkInt(...$arg);
    }
}
