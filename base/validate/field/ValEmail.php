<?php

namespace agileFW\base\validate\field;

require_once __DIR__ . '/autoload.php';

use agileFW\base\util\Results;
use agileFW\base\data\model\Model;
use agileFW\base\validate\field\ValField;

/**
 * パスワードのバリデーション  
 * 半角英数字記号のみを許可する。
 */
class ValEmail extends ValField {
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
     * mandatory,checkHan,checkStr,checkEmail の順に呼び出す
     */
    public function check(bool $mandatory = false, int|string ...$arg): self {
        $this->mandatory($mandatory)->checkHan()->checkStr();
        return $this->checkEmail();
    }

    /**
     * Eメールアドレスの正規表現
     * RFC 5322に基づく基本的な形式を許可
     */
    private const string EMAIL_REGEX = '/^[a-zA-Z0-9_+-]+(\.[a-zA-Z0-9_+-]+)*@([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/';

    /**
     * フィールドの値をEメールアドレスとして検証する
     * @return self 検証インスタンス
     */
    protected function checkEmail(): self {
        if ($this->value === null) return $this;

        if (preg_match($this->value, self::EMAIL_REGEX) === 0) {
            $this->error($this->name, $this->jpName . 'はEメールアドレスの書式に合いません。');
        }
        return $this;
    }
}
