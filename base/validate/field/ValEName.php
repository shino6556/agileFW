<?php

namespace agileFW\base\validate\field;

require_once __DIR__ . '/autoload.php';

use agileFW\base\util\Results;
use agileFW\base\util\StrUtil;
use agileFW\base\data\model\Model;
use agileFW\base\validate\field\ValField;

/**
 * 英語名のバリデーション  
 * 半角英数字とハイフン、ピリオドのみを許可する。
 */
class ValEName extends ValField {
    public function __construct() {
    }
    /**
     * @param Model $model 検証対象のモデル
     * @param ?array $row 検証対象の配列 
     * @param string $fieldName フィールド名
     * @param Results $results 検証結果保持
     * @return ValEname 検証インスタンス
     */
    public static function start(Model $model, ?array $row, string $fieldName, Results $results): ValEName {
        return self::startBase(self::$all, $model, $row, $fieldName, $results);
    }
    private static array $all = [];

    /**
     * @inheritDoc
     * mandatory,checkHan,checkStr,checkENameの順に呼び出す
     */
    public function check(bool $mandatory = false, int|string ...$arg): ValEName {
        $this->mandatory($mandatory)->checkHan()->checkStr();
        return $this->checkEName();
    }

    /**
     * フィールドの値を英語名として検証する
     * @return ValEName 英語名検証インスタンス
     */
    protected function checkEName(): ValEName {
        if ($this->value === null) return $this;

        // ハイフンとピリオドを許可する
        $han = substr(StrUtil::HAN_SYM, 0, strlen(StrUtil::HAN_SYM) - 2);
        if (StrUtil::contains($this->value, $han)) {
            $this->error($this->name, $this->jpName . 'は半角ハイフン、ピリオド以外の記号を含んではなりません。');
        }
        return $this;
    }
}
