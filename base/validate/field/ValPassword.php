<?php

namespace agileFW\base\validate\field;

require_once __DIR__ . '/autoload.php';

use agileFW\base\util\Results;
use agileFW\base\util\StrUtil;
use agileFW\base\data\model\Model;
use agileFW\base\validate\field\ValField;

/**
 * パスワードのバリデーション  
 * 半角英数字記号のみを許可する。
 */
class ValPassword extends ValField {
    public function __construct() {
    }
    /**
     * @param Model $model 検証対象のモデル
     * @param ?array $row 検証対象の配列 
     * @param string $fieldName フィールド名
     * @param Results $results 検証結果保持
     * @return self 検証インスタンス
     */
    public static function start(Model $model, ?array $row, string $fieldName, Results $results): self {
        return self::startBase(self::$all, $model, $row, $fieldName, $results);
    }
    private static array $all = [];

    /**
     * @inheritDoc
     * mandatory,checkHan,checkStr,checkPassword の順に呼び出す
     */
    public function check(bool $mandatory = false, int|string ...$arg): self {
        $this->mandatory($mandatory)->checkHan()->checkStr();
        return $this->checkPassword();
    }

    /**
     * フィールドの値をパスワードとして検証し、ハッシュ値に変換する
     * @return self 検証インスタンス
     */
    protected function checkPassword(): self {
        if ($this->value === null) return $this;

        if (StrUtil::contains($this->value, StrUtil::HAN_ALPH) === 0) {
            $this->error($this->name, $this->jpName . 'は1文字以上の半角英字を含まなければなりません。');
        }
        if (StrUtil::contains($this->value, StrUtil::HAN_NUM) === 0) {
            $this->error($this->name, $this->jpName . 'は1文字以上の半角数字を含まなければなりません。');
        }
        if (StrUtil::contains($this->value, StrUtil::HAN_SYM) === 0) {
            $this->error($this->name, $this->jpName . 'は1文字以上の半角記号を含まなければなりません。');
        }
        if ($this->results->isOk()) {
            // パスワードをハッシュ化する
            $this->value = password_hash($this->value, PASSWORD_DEFAULT);
        }
        return $this;
    }
}
