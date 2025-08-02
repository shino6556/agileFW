<?php

namespace agileFW\base\validate\field;

require_once __DIR__ . '/autoload.php';

use agileFW\base\util\Results;
use agileFW\base\util\StrUtil;
use agileFW\base\data\model\Model;
use agileFW\base\validate\field\ValField;

/**
 * 日本語名のバリデーション  
 * 全角文字のみを許可する。
 */
class ValJpName extends ValField {
    /**
     * @param Model $model 検証対象のモデル
     * @param ?array $row 検証対象の配列 
     * @param string $fieldName フィールド名
     * @param Results $results 検証結果保持
     * @return ValJpName 検証インスタンス
     */
    public static function start(Model $model, ?array $row, string $fieldName, Results $results): ValJpName {
        return self::startBase(self::$all, $model, $row, $fieldName, $results);
    }
    private static array $all = [];

    /**
     * @inheritDoc
     * mandatory,checkZen,checkStr,checkJpNameの順に呼び出す
     */
    public function check(bool $mandatory = false, int|string ...$arg): ValJpName {
        $this->mandatory($mandatory)->checkZen()->checkStr();
        return $this->checkJpName();
    }

    /**
     * フィールドの値を英語名として検証する
     * @return ValJpName 日本語名検証インスタンス
     */
    protected function checkJpName(): ValJpName {
        if ($this->value === null) return $this;

        // ハイフンとピリオドを許可する
        if (StrUtil::contains($this->value, StrUtil::ZEN_SYM)) {
            $this->error($this->name, $this->jpName . 'は記号を含んではなりません。');
        }
        return $this;
    }
}
