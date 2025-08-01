<?php

namespace Nnk2\Base\data\model;

require_once __DIR__ . '/autoload.php';

use Nnk2\Base\Util\Types;
use Nnk2\Base\data\db\DbTypes;

/**
 * モデルのフィールドを表すクラス  
 * モデルのフィールドは、モデルの属性を定義するために使用される。
 * フィールドは、モデルのデータ型、列名、表示名などを定義する。
 */
class Field {
    /**
     * モデルの参照フィールドを生成する
     * @param string $name フィールド名
     * @param string $displayName 表示名
     * @param string $modelName モデル名
     * @param string $modelId モデルIDのフィールド名
     * @return Field フィールドインスタンス
     */
    public static function ref(
        string $name,
        string $displayName,
        string $modelName,
        string $modelId,
    ): Field {
        $field = new self($name, '', $displayName, Types::MODEL);
        $field->modelName = $modelName;
        $field->modelId = $modelId;
        return $field;
    }
    /**
     * 新しいフィールドを生成する
     * @param string $name フィールド名
     * @param string $column DB上の列名
     * @param string $displayName フィールドの表示名
     * @param Types $type フィールドのデータ型
     * @param DbTypes $dbType データベースのデータ型
     * @param ?int $min 最小値
     * @param ?int $max 最大値
     * @param mixed $defaultValue デフォルト値
     * @return Field フィールドインスタンス
     */
    public static function new(
        string $name,
        string $column,
        string $displayName,
        Types $type = Types::STRING,
        DbTypes $dbType = DbTypes::VARCHAR,
        ?int $min = null,
        ?int $max = null,
        mixed $defaultValue = null
    ): Field {
        return new self($name, $column, $displayName, $type, $dbType, $min, $max, $defaultValue);
    }
    /**
     * コンストラクタ
     * @param string $name フィールド名
     * @param string $column DB上の列名
     * @param string $displayName フィールドの表示名
     * @param Types $type フィールドのデータ型
     * @param DbTypes $dbType データベースのデータ型
     * @param ?int $min 最小値
     * @param ?int $max 最大値
     * @param mixed $defaultValue デフォルト値
     */
    private function __construct(
        string $name,
        string $column,
        string $displayName,
        Types $type = Types::STRING,
        DbTypes $dbType = DbTypes::VARCHAR,
        ?int $min = null,
        ?int $max = null,
        mixed $defaultValue = null
    ) {
        $this->name = $name;
        $this->column = $column;
        $this->displayName = $displayName;
        $this->type = $type;
        $this->dbType = $dbType;
        $this->min = $min;
        $this->max = $max;
        $this->defaultValue = $defaultValue;
    }
    /** @var string フィールド名 */
    public string $name;
    /** @var string DB上の列名 */
    public string $column;
    /** @var string フィールドの表示名 */
    public string $displayName;
    /** @var Types フィールドのデータ型 */
    public Types $type;
    /** @var DbTypes データベースのデータ型 */
    public DbTypes $dbType;
    /** @var int 最小値 */
    public ?int $min = null;
    /** @var int 最大値 */
    public ?int $max = null;
    /** @var mixed デフォルト値 */
    public mixed $defaultValue = null;
    /** @var string モデル名 */
    public string $modelName;
    /** @var string モデルIDのフィールド名 */
    public string $modelId;
}
