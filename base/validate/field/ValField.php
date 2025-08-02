<?php

namespace nnk2\base\validate\field;

require_once __DIR__ . '/autoload.php';

use nnk2\base\util\Results;
use nnk2\base\util\StrUtil;
use nnk2\base\data\model\Model;
use nnk2\base\data\model\Field;


/**
 * フィールド種別単位のバリデーションの基底クラス  
 * このクラスを継承して、各種フィールドのノーマライズとバリデーションを実装する。  
 * バリデーションは、種別ごとのフィールドの値を変換・検証するために使用される。
 * ```php
 * $results = ValPostal::start($postal,$results)->check(true)->end();
 * ```
 */
abstract class ValField {
    /**
     * フィールド単位の検証インスタンスにキャストする
     * @param mixed $obj オブジェクト
     * @return ?ValField 
     */
    public static function castValField(mixed $obj): ?ValField {
        if ($obj instanceof ValField) {
            return $obj;
        }
        return null;
    }

    /**
     * バリデーションのインスタンスを取得する
     * @param array &$all ValFieldが登録される配列
     * @param Model $model 対象のモデル
     * @param ?array $row 対象の配列 
     * @param string $fieldName フィールド名
     * @param Results $results 処理結果を保持するインスタンス
     * @return ValField バリデーションのインスタンス
     */
    public static function startBase(array &$all, Model $model, ?array $row, string $fieldName, Results $results): ValField {
        $self = ValField::castValField($all[$fieldName]);
        if (!$self) {
            // まだなければ作成する
            $self = new static();
            $all[$fieldName] = $self;
        }
        $self->model   = $model;
        $self->results = $results;
        $self->name    = $fieldName;
        $self->fields  = $model->fields();
        $self->row     = $row;

        $field = Field::cast($self->fields[$fieldName]);
        if (!$field) {
            // フィールドが定義されていない場合はエラー
            $self->results->error(__METHOD__, 'フィールドが定義されていません。', $fieldName);
        }
        $self->value    = $self->get($fieldName);
        $self->jpName   = $field->jpName;
        $self->min      = $field->min;
        $self->max      = $field->max;
        $self->defValue = $field->defValue;
        return $self;
    }
    protected Model $model;
    protected Results $results;
    protected array $fields;
    protected ?array $row;

    protected mixed $value = null;
    protected string $name;
    protected string $jpName;
    protected ?int $min = null; // 最小値
    protected ?int $max = null; // 最大値
    protected mixed $defValue = null;

    /**
     * コンストラクタ
     * 
     * インスタンス生成は static ファクトリメソッドからのみ許可するため private。
     */
    private function __construct() {
    }

    /**
     * モデル or 配列から値を取得する
     * @param ?string $nameフィールド名 省略=null:$this->nameを使用
     * @return mixed 値
     */
    protected function get(?string $name = null): mixed {
        $name = $name ?? $this->name;
        $value = null;
        if ($this->row) {
            $value = $this->row[$name] ?? null;
        } else {
            $value = $this->model->getValue($name);
        }
        return $value;
    }

    /**
     * モデル or 配列へ値をセットする
     * @param mixed value 値
     * @param ?string $nameフィールド名 省略=null:$this->nameを使用
     * @return ValField 検証インスタンス
     */
    protected function set(mixed $value, ?string $name = null): ValField {
        $name = $name ?? $this->name;
        if ($this->row) {
            $this->row[$name] = $value;
        } else {
            $this->model->setValue($name, $value);
        }
        return $this;
    }
    /**
     * 他のフィールドの日本語名を返す
     * @param string $name 他のフィールド名
     * @return string 日本語名(見つからないと$name)
     */
    protected function jpName(string $name): string {
        $field = Field::cast($this->fields[$name]);
        $jpName = $field === null ? $name : $field->jpName;
        return $jpName;
    }

    /**
     * フィールドの値の変換・検証を行う
     * @param bool $mandatory true:必須 省略=false:任意
     * @return ValField 検証インスタンス
     */
    abstract public function check(bool $mandatory = false): ValField;

    /**
     * バリデーションの結果を返す
     * @return bool true:バリデーション成功
     */
    public function end(): bool {
        $this->set($this->value); // 値をモデル|配列にセット
        return $this->results->isOk();
    }

    /**
     * フィールドの値が必須であることを検証する
     * @param bool $mandatory 必須かどうか 省略 = false:任意
     * @return ValField 検証インスタンス
     */
    protected function mandatory(bool $mandatory = false): ValField {
        if ($mandatory) {
            if ($this->value === null || $this->value === '') {
                $this->error($this->name, $this->jpName . 'は必須です。');
            }
        }
        return $this;
    }
    /**
     * フィールドの値が半角英数字であることを検証する
     * @return ValField 検証インスタンス
     */
    protected function checkHan(): ValField {
        if ($this->value === null) return $this;

        $org = $this->value;
        if (is_string($this->value)) {
            $this->value = StrUtil::zenHan($this->value, StrUtil::CNV_HAN_ALPH, StrUtil::CNV_HAN_NUM, StrUtil::CNV_HAN_SYM);
        }
        if ($org !== $this->value) {
            $this->warning($this->name, $this->jpName . 'を半角英数字に変換しました。');
        }
        if (StrUtil::isASCII($this->value) === false) {
            $this->error($this->name, $this->jpName . 'は半角英数字でなければなりません。');
        }
        return $this;
    }

    /**
     * フィールドの値が全角文字であることを検証する
     * @return ValField 検証インスタンス
     */
    protected function checkZen(): ValField {
        if ($this->value === null) return $this;

        $org = $this->value;
        if (is_string($this->value)) {
            $this->value = StrUtil::zenHan($this->value, StrUtil::CNV_ZEN_ALPH, StrUtil::CNV_ZEN_NUM, StrUtil::CNV_ZEN_SYM, StrUtil::CNV_ZEN_HIRA);
        }
        if ($org !== $this->value) {
            $this->warning($this->name, $this->jpName . 'を全角文字に変換しました。');
        }
        if (StrUtil::contains($this->value, StrUtil::HAN_ALNUMSYM) > 0) {
            $this->error($this->name, $this->jpName . 'は全角文字でなければなりません。');
        }
        return $this;
    }
    /**
     * フィールドの値を文字列として検証する
     * @return ValField 検証インスタンス
     */
    protected function checkStr(): ValField {
        if ($this->value === null) return $this;

        if (!is_string($this->value)) {
            $this->error($this->name, $this->jpName . 'は文字列でなければなりません。');
        } else if (mb_strlen($this->value) < $this->min) {
            $this->error($this->name, $this->jpName . 'は' . $this->min . '文字以上でなければなりません。');
        } else if (mb_strlen($this->value) > $this->max) {
            $this->error($this->name, $this->jpName . 'は' . $this->max . '文字以下でなければなりません。');
        }
        return $this;
    }
    /**
     * フィールドの値を整数として検証する
     * @return ValField 検証インスタンス
     */
    protected function checkInt(int ...$values): ValField {
        if ($this->value === null) return $this;
        $org = $this->value;

        if (StrUtil::isIntStr($this->value)) {
            $this->value = intval($org);
        } else {
            $this->error($this->name, $this->jpName . 'は数値でなければなりません。');
        }
        if ($this->value < $this->min) {
            $this->error($this->name, $this->jpName . 'は' . $this->min . '以上でなければなりません。');
        } else if ($this->value > $this->max) {
            $this->error($this->name, $this->jpName . 'は' . $this->max . '以下でなければなりません。');
        }
        if ($values && in_array($this->value, $values) === false) {
            $this->error($this->name, $this->jpName . 'は[' . implode(',', $values) . ']のどれかでなければなりません。');
        }
        return $this;
    }

    /**
     * エラーの登録
     * @param string $field エラーとなったフィールド名
     * @param string $msg エラーメッセージ
     * @return void
     */
    protected function error(string $field, string $msg): void {
        $this->results->error($field, $msg, $this->value);
    }
    /**
     * 警告の登録
     * @param string $field 警告となったフィールド名
     * @param string $msg 警告メッセージ
     * @return void
     */
    protected function warning(string $field, string $msg): void {
        $this->results->warning($field, $msg, $this->value);
    }
    /**
     * トレースの登録
     * @param string $field トレースしたフィールド名
     * @param string $msg トレースメッセージ
     * @return void
     */
    protected function trace(string $field, string $msg): void {
        $this->results->trace($field, $msg, $this->value);
    }
}
