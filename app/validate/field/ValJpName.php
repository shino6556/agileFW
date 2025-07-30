<?php

namespace nnk2\app\validate\field;

require_once __DIR__ . '/autoload.php';

use nnk2\base\util\Results;
use nnk2\base\util\StrUtil;
use nnk2\base\data\model\Model;
use nnk2\base\validate\field\ValField;

/**
 * 日本語名のバリデーション  
 * 全角文字のみを許可する。
 */
class ValJpName extends ValField {
    public function __construct() {
    }
    /**
     * @param Model $model 検証対象のモデル
     * @param Results $results 検証結果保持
     * @param string $fieldName フィールド名
     * @param ?array $row 検証対象の配列 省略=null 
     * @return ValJpName 検証インスタンス
     */
    public static function start(Model $model, Results $results, string $fieldName, ?array $row = null): ValJpName {
        return self::startBase(self::$all, $model, $results, $fieldName, $row);
    }
    private static array $all = [];

    /**
     * @inheritDoc
     */
    public function check(bool $mandatory = false): ValJpName {
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
        $han = substr(StrUtil::HAN_SYM, 0, strlen(StrUtil::HAN_SYM) - 2);
        if (StrUtil::contains($this->value, $han)) {
            $this->error($this->name, $this->jpName . 'は半角ハイフン、ピリオド以外の記号を含んではなりません。');
        }
        return $this;
    }
}
