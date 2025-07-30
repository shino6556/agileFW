<?php

namespace Nnk2\Base\data\model;

require_once __DIR__ . '/autoload.php';

use DateTime;
use Nnk2\Base\Util\ArrayUtil;
use Nnk2\Base\Util\Results;
use Nnk2\Base\Util\StrUtil;
use Nnk2\Base\Util\TypeUtil;
use Nnk2\Base\Util\Types;
use Nnk2\Base\logic\Logic;
use Nnk2\Base\data\db\DbTypes;

/**
 * モデルの抽象基底クラス
 * フィールドの値はnullを必ず含めること。
 * デフォルトで全フィールドをANDで繋いだ検索定義を持つ。
 * @abstract
 */
abstract class Model {
	/**
	 * コンストラクタ
	 * @param int $pkey 主キー (省略 = 0)
	 * 主キーが0の場合、モデルは新規作成される。
	 */
	public function __construct(int $pkey = 0) {
		$this->pkey = $pkey === 0 ? self::$psuedoPkey-- : $pkey;
		$this->activated = false;
	}
	/** @var int 仮主キー */
	private static int $psuedoPkey = -1; // 仮主キー

	/**
	 * モデル名を返す
	 * @return string モデル名
	 * @abstract
	 */
	abstract public function modelName(): string;

	/**
	 * 固有フィールド名の一覧を返す
	 * @return array フィールド名の一覧
	 * @abstract
	 */
	abstract protected function getOwnFields(): array;

	/**
	 * フィールド定義の一覧を返す
	 * @return array フィールド定義の一覧
	 * @abstract
	 */
	public function getFields(): array {
		if ($this->fields === null) {
			$this->fields = ArrayUtil::append(self::COMMON_FIELDS, $this->getOwnFields());
		}
		return $this->fields;
	}
	/** @var ?array 共通＋固有フィールド */
	private ?array $fields = null;


	protected function field(string $name): ?array {
		$field = ArrayUtil::get($this->getFields(), $name);
		if (!$field) {
			Results::self()->error(__METHOD__, 'error.field.not_found' . $this->modelName() . '::' . $name);
			return null;
		} else {
			return $field;
		}
	}

	/** @var int フィールド配列:フィールド名 */
	public const int FLD_FIELD_NAME = 0;
	/** @var int フィールド配列:DBカラム名 */
	public const int FLD_COLUMN_NAME = 1;
	/** @var int フィールド配列:日本語名 */
	public const int FLD_JAPANESE_NAME = 2;
	/** @var int フィールド配列:PHPデータ型 */
	public const int FLD_DATA_TYPE = 3;
	/** @var int フィールド配列:DBデータ型 */
	public const int FLD_DATA_DBTYPE = 4;
	/** @var int フィールド配列:最小値 */
	public const int FLD_MIN = 5;
	/** @var int フィールド配列:最大値 */
	public const int FLD_MAX = 6;
	/** @var int フィールド配列:初期値 */
	public const int FLD_DEF = 7;
	/** @var int フィールド配列:関連先のモデル名 */
	public const int FLD_MODEL_NAME = 8;
	/** @var int フィールド配列:関連先のモデルのpkeyを保持するフィールド */
	public const int FLD_MODEL_PKEY = 9;

	/**
	 * DB上のカラム名を返す
	 * @param string $name フィールド名
	 * @return string DB上のカラム名
	 */
	public function column(string $name): ?string {
		$field = $this->field($name);
		return $field ? $field[self::FLD_COLUMN_NAME] : null;
	}
	/**
	 * フィールドの日本語名を返す
	 * @param string $name フィールド名
	 * @return string フィールドの日本語名
	 */
	public function jpName(string $name): ?string {
		$field = $this->field($name);
		return $field ? $field[self::FLD_JAPANESE_NAME] : null;
	}
	/**
	 * フィールドのデータ型を返す
	 * @param string $name フィールド名
	 * @return ?Types フィールドのデータ型
	 */
	public function type(string $name): ?Types {
		$field = $this->field($name);
		return $field ? $field[self::FLD_DATA_TYPE] : null;
	}
	/**
	 * フィールドのDBデータ型を返す
	 * @param string $name フィールド名
	 * @return ?DbTypes フィールドのDBデータ型
	 */
	public function dbType(string $name): ?DbTypes {
		$field = $this->field($name);
		return $field ? $field[self::FLD_DATA_DBTYPE] : null;
	}
	/**
	 * フィールドの最小値を返す
	 * @param string $name フィールド名
	 * @return ?int フィールドの最小値
	 */
	public function min(string $name): ?int {
		$field = $this->field($name);
		return $field ? $field[self::FLD_MIN] : null;
	}
	/**
	 * フィールドの最大値を返す
	 * @param string $name フィールド名
	 * @return ?int フィールドの最大値
	 */
	public function max(string $name): ?int {
		$field = $this->field($name);
		return $field ? $field[self::FLD_MAX] : null;
	}
	/**
	 * フィールドの規定値を返す
	 * @param string $name フィールド名
	 * @return mixed フィールドの規定値
	 */
	public function def(string $name): mixed {
		$field = $this->field($name);
		return $field ? $field[self::FLD_DEF] : null;
	}
	/**
	 * フィールドの関連先モデル名を返す
	 * @param string $name フィールド名
	 * @return ?string 関連先モデル名 null:関連なし
	 */
	public function refModel(string $name): ?string {
		$field = $this->field($name);
		return $field ? ArrayUtil::get($field, self::FLD_MODEL_NAME) : null;
	}
	/**
	 * フィールドの関連先モデルのpkeyを保持するフィールド名を返す
	 * @param string $name フィールド名
	 * @return ?string 関連先モデルのpkeyを保持するフィールド名 null:関連なし
	 */
	public function refModelPkey(string $name): ?string {
		$field = $this->field($name);
		return $field ? ArrayUtil::get($field, self::FLD_MODEL_PKEY) : null;
	}

	/**
	 * フィールド名の一覧を返す
	 * @param bool $onlyOwnField true:pkeyと固有フィールドのみ (省略 = false:全フィールド)
	 * @return array フィールド名の一覧
	 */
	public function getFieldNames(bool $onlyOwnField = false): array {
		if ($onlyOwnField) {
			$fields = [self::pkey];
			return ArrayUtil::append($fields, array_keys(self::$fields));
		}
		return array_keys($this->getFields());
	}
	/** @var string 主キー名 */
	public const string pkey = 'pkey';
	/** @var string 作成日 */
	public const string createDate = 'createDate';
	/** @var string 更新日 */
	public const string updateDate = 'updateDate';
	/** @var string 削除フラグ */
	public const string deleteFlag = 'deleteFlag';

	/** @var array 共通フィールド */
	private const array COMMON_FIELDS = [
		self::pkey       => [self::pkey,      'pkey',         '主キー',     Types::INT,      null, null, DbTypes::INT],
		self::createDate => [self::createDate, 'create_date', '作成日',     Types::DATETIME, null, null, DbTypes::TIMESTAMP],
		self::updateDate => [self::updateDate, 'update_date', '更新日',     Types::DATETIME, null, null, DbTypes::TIMESTAMP],
		self::deleteFlag => [self::deleteFlag, 'delete_flag', '削除フラグ', Types::BOOL,     null, null, DbTypes::BOOL],
	];

	/**
	 * 基底モデルにキャストする
	 * @param mixed $var キャストする変数
	 * @return ?Model $model 基底モデル null:キャスト失敗
	 */
	public static function castModel(mixed $var): ?Model {
		return ($var instanceof Model) ? $var : null;
	}

	/**
	 * 連想配列からフィールドに値を読み込む
	 * @param array $values フィールド名をキーとした連想配列
	 * @param bool $overwrite true:値があっても上書きする (省略 = false)
	 * @param bool $update true:値をセットしたら更新対象にする (省略 = true)
	 */
	public function fromArray(array $values, bool $overwrite = false, bool $update = true) {
		foreach ($this->getFieldNames(true) as $field) {
			$val = $this->getValue($field);
			/// 値を持っていて上書き禁止の場合、スキップ
			if ($overwrite === false && $val !== null) continue;

			$value = ArrayUtil::get($values, $field);
			$this->setValue($field, $value, $update);
		}
		$this->activate(true);
	}

	/**
	 * 連想配列にフィールドの値を書き出す
	 * @param ?array $fields 書き出すフィールド名の配列 null:全フィールド
	 * @return array [フィールド名=>値]
	 */
	public function toArray(?array $fields = null): array {
		$values = [];
		$targetFields = $fields ?? $this->getFields();
		foreach ($targetFields as $field) {
			// モデルを保持するフィールドは書き込まない
			$type = $this->getTypes($field);
			if ($type == Types::MODEL) continue;

			// 値の取得
			$val = $this->getValue($field);

			// 主キーがマイナスは新規作成なので、書き出さない
			if ($field == self::pkey && $val < 0) continue;

			// 値を連想配列にセット
			$values[$field] = $val;
		}
		return $values;
	}

	/**
	 * データ型の配列を返す
	 * @param ?array $values 更新対象の値配列 null:全フィールド
	 * @return array [フィールド名=>データ型]
	 */
	public function getTypes(?array $values = null): array {
		$types = [];
		if ($values) {
			$names = array_keys($values);
			$fields = $this->getFields();
			foreach ($names as $name) {
				$info = ArrayUtil::get($fields, $name);
				$types[$name] = $info[self::FLD_DATA_TYPE];
			}
		} else {
			$fields = $this->getFields();
			foreach ($fields as $name => $info) {
				$types[$name] = $info[self::FLD_DATA_TYPE];
			}
		}
		return $types;
	}

	/**
	 * このモデルが依存しているモデル
	 * 相手を先に新規作成して、主キーを有効にする
	 * @return array [フィールド名 => [対象モデル名,対象モデル主キー], ...]
	 */
	public function getDependModel(): array {
		$depends = [];
		foreach ($this->getFields() as $field => $info) {
			$modelName = ArrayUtil::get($info, self::FLD_MODEL_NAME);
			$modelPkey = ArrayUtil::get($info, self::FLD_MODEL_PKEY);
			if ($modelName) {
				$depends[$field] = [$modelName, $modelPkey];
			}
		}
		return $depends;
	}

	//// プロパティのセット/取得 ////

	public function setPkey(int $val) {
		$this->pkey = $val;
	}
	public function getPkey(): int {
		return $this->pkey;
	}
	private int $pkey = 0;

	public function setCreateDate(?DateTime $val, bool $update = true) {
		$this->actBase($update)->createDate = $val;
	}
	public function getCreateDate(): ?DateTime {
		return $this->actBase()->createDate;
	}
	private ?DateTime $createDate = null;

	public function setUpdateDate(?DateTime $val, bool $update = true) {
		$this->actBase($update)->updateDate = $val;
	}
	public function getUpdateDate(): ?DateTime {
		return $this->actBase()->updateDate;
	}
	private ?DateTime $updateDate = null;

	public function setDeleteFlag(?int $val, bool $update = true) {
		$this->actBase($update)->deleteFlag = $val;
	}
	public function getDeleteFlag(): ?int {
		return $this->actBase()->deleteFlag;
	}
	private ?int $deleteFlag = null;

	/**
	 * モデルを活性化する
	 * @param bool $activated true:活性化する (省略 = true)
	 * @return Model
	 */
	protected function activate(bool $activated = true): Model {
		$this->activated = $activated;
		return $this;
	}
	/**
	 * モデルが活性化しているか否か
	 * @return bool true:活性化している
	 */
	public function isActivated(): bool {
		return $this->activated;
	}
	/**
	 * モデルを非活性化する
	 */
	public function inactivate() {
		$this->activated = false;
		$logic = $this->logic();
		$logic->removeUpdate($this);
	}
	private bool $activated = false;


	/**
	 * モデルの値をロードし活性化する。
	 * 派生クラスで自クラスにキャストするものをオーバーロードする
	 * ```php
	 * private function act(): User { return $this->actBase(); }
	 * ```
	 * @param bool $isSetter true:setterから呼ばれた (省略=false,getterから呼ばれた)
	 * @return Model
	 */
	protected function actBase(bool $isSetter = false): Model {
		$logic = $this->logic();
		if ($logic === null) return $this;

		if ($isSetter) {
			// ロジックに登録する
			$logic->register($this, $isSetter);
		} else if ($this->activated === false) {
			// ロジックをロードする
			$logic->load($this->activate());
		}
		return $this;
	}

	protected function logic(): Logic {
		$logic = Logic::getLogic($this->modelName() . 'Logic');
		return $logic;
	}

	/**
	 * フィールドの値を取得する
	 * @param string $field フィールド名
	 * @return mixed フィールドの値
	 */
	public function getValue(string $field): mixed {
		$getter = 'get' . StrUtil::upper1st($field);
		$value = $this->$getter();
		return $value;
	}
	/**
	 * フィールドに値をセットする
	 * @param string $field フィールド名
	 * @param mixed $value セットする値
	 * @param bool $update true:更新対象にする (省略 = true)
	 * @return Model this
	 */
	public function setValue(string $field, mixed $value, bool $update = true): Model {
		$setter = 'set' . StrUtil::upper1st($field);
		$this->$setter($value, $update);
		return $this;
	}
}
