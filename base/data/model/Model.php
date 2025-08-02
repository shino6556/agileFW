<?php

namespace agileFW\Base\data\model;

require_once __DIR__ . '/autoload.php';

use DateTime;
use agileFW\Base\Util\ArrayUtil;
use agileFW\Base\Util\Results;
use agileFW\Base\Util\StrUtil;
use agileFW\Base\Util\Types;
use agileFW\Base\logic\Logic;
use agileFW\Base\data\db\DbTypes;

/**
 * モデルの抽象基底クラス
 * @abstract
 */
abstract class Model {
	/**
	 * コンストラクタ
	 * @param int $pkey 主キー (省略 = 0 :新規作成)
	 * 主キーが0の場合、モデルは新規作成される。
	 * モデルはnewModel()で生成されるため、コンストラクタは非公開。
	 */
	protected function __construct(int $pkey = 0) {
		$this->pkey = $pkey === 0 ? self::$psuedoPkey-- : $pkey;
		$this->activated = false;
	}
	/** @var int 仮主キー */
	private static int $psuedoPkey = -1; // 仮主キー

	/**
	 * モデルを登録する  
	 * すでに登録済みのモデルは、登録したものに置き換えられる  
	 * 主キーが0の場合、モデルは新規作成される。
	 * @param Model $model モデル (pkey = 0 :新規作成)
	 * @return Model 新規作成/取得されたモデル
	 */
	public static function newModel(Model $model): Model {
		$model = $model->getLogicBase()->register($model, false);
		return $model;
	}

	/**
	 * モデル名を返す
	 * @return string モデル名
	 * @abstract
	 */
	abstract public function modelName(): string;

	/**
	 * 基底ロジックを取得する
	 * @return Logic 基底ロジック
	 */
	abstract public function getLogicBase(): Logic;

	/**
	 * 固有フィールド定義の一覧を返す
	 * @return array 固有フィールド定義の一覧
	 * @abstract
	 */
	abstract protected function ownFields(): array;

	/**
	 * フィールド定義の一覧を返す
	 * @return array フィールド定義の一覧
	 */
	final public function fields(): array {
		$fieldDefs = ArrayUtil::append($this->commonFields(), $this->ownFields());
		return $fieldDefs;
	}

	/** @var string 主キー名 */
	public const string pkey = 'pkey';
	/** @var string 作成日 */
	public const string createDate = 'createDate';
	/** @var string 更新日 */
	public const string updateDate = 'updateDate';
	/** @var string 削除フラグ */
	public const string deleteFlag = 'deleteFlag';

	/**
	 * 共通フィールド定義の一覧を返す
	 * @return array 共通フィールド定義の一覧
	 */
	final protected function commonFields(): array {
		if (empty(self::$commonFieldsDef)) {
			self::$commonFieldsDef[self::pkey]       = Field::new(self::pkey,      'pkey',         '主キー',     Types::INT,      DbTypes::INT);
			self::$commonFieldsDef[self::createDate] = Field::new(self::createDate, 'create_date', '作成日',     Types::DATETIME, DbTypes::TIMESTAMP);
			self::$commonFieldsDef[self::updateDate] = Field::new(self::updateDate, 'update_date', '更新日',     Types::DATETIME, DbTypes::TIMESTAMP);
			self::$commonFieldsDef[self::deleteFlag] = Field::new(self::deleteFlag, 'delete_flag', '削除フラグ', Types::BOOL,     DbTypes::BOOL, null, null, false);
		}
		return self::$commonFieldsDef;
	}
	private static array $commonFieldsDef = [];

	/**
	 * モデルのフィールド定義を返す
	 * @param  string $name フィールド名
	 * @return Field モデルのフィールド定義
	 */
	final public function field(string $name): ?Field {
		$field = ArrayUtil::get($this->fields(), $name);
		if (!$field) {
			Results::self()->error(__METHOD__, 'error.field.not_found' . $this->modelName() . '::' . $name);
			return null;
		} else {
			return $field;
		}
	}

	/**
	 * DB上のカラム名を返す
	 * @param string $name フィールド名
	 * @return string DB上のカラム名
	 */
	public function column(string $name): ?string {
		$field = $this->field($name);
		return $field ? $field->column : null;
	}
	/**
	 * フィールドの日本語名を返す
	 * @param string $name フィールド名
	 * @return string フィールドの日本語名
	 */
	public function jpName(string $name): ?string {
		$field = $this->field($name);
		return $field ? $field->jpName : null;
	}
	/**
	 * フィールドのデータ型を返す
	 * @param string $name フィールド名
	 * @return ?Types フィールドのデータ型
	 */
	public function type(string $name): ?Types {
		$field = $this->field($name);
		return $field ? $field->type : null;
	}
	/**
	 * フィールドのDBデータ型を返す
	 * @param string $name フィールド名
	 * @return ?DbTypes フィールドのDBデータ型
	 */
	public function dbType(string $name): ?DbTypes {
		$field = $this->field($name);
		return $field ? $field->dbType : null;
	}
	/**
	 * フィールドの最小値を返す
	 * @param string $name フィールド名
	 * @return ?int フィールドの最小値
	 */
	public function min(string $name): ?int {
		$field = $this->field($name);
		return $field ? $field->min : null;
	}
	/**
	 * フィールドの最大値を返す
	 * @param string $name フィールド名
	 * @return ?int フィールドの最大値
	 */
	public function max(string $name): ?int {
		$field = $this->field($name);
		return $field ? $field->max : null;
	}
	/**
	 * フィールドの規定値を返す
	 * @param string $name フィールド名
	 * @return mixed フィールドの規定値
	 */
	public function def(string $name): mixed {
		$field = $this->field($name);
		return $field ? $field->defValue : null;
	}
	/**
	 * フィールドの関連先モデル名を返す
	 * @param string $name フィールド名
	 * @return ?string 関連先モデル名 null:関連なし
	 */
	public function refModel(string $name): ?string {
		$field = $this->field($name);
		return $field ? $field->modelName : null;
	}
	/**
	 * フィールドの関連先モデルのpkeyを保持するフィールド名を返す
	 * @param string $name フィールド名
	 * @return ?string 関連先モデルのpkeyを保持するフィールド名 null:関連なし
	 */
	public function refModelPkey(string $name): ?string {
		$field = $this->field($name);
		return $field ? $field->modelId : null;
	}

	/**
	 * フィールド名の一覧を返す
	 * @param bool $onlyOwnFields true:pkeyと固有フィールドのみ (省略 = false:全フィールド)
	 * @return array フィールド名の一覧
	 */
	public function getFieldNames(bool $onlyOwnFields = false): array {
		if ($onlyOwnFields) {
			$fields = [self::pkey => 1];
			$fields = ArrayUtil::append($fields, $this->ownFields());
		} else {
			$fields = $this->fields();
		}
		return array_keys($fields());
	}

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
		$targetFields = $fields ?? $this->fields();
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
			$fields = $this->fields();
			foreach ($names as $name) {
				$info = ArrayUtil::get($fields, $name);
				$types[$name] = $info->type ?? Types::STRING; // デフォルトは文字列型
			}
		} else {
			$fields = $this->fields();
			foreach ($fields as $name => $info) {
				$types[$name] = $info->dbType ?? DbTypes::VARCHAR; // デフォルトは文字列型
			}
		}
		return $types;
	}

	/**
	 * このモデルが依存しているモデル
	 * 相手を先に新規作成して、主キーを有効にする
	 * @return array [フィールド名 => [対象モデル名,対象モデル主キー], ...]
	 */
	public function dependModel(): array {
		$depends = [];
		foreach ($this->fields() as $field => $info) {
			$modelName = $info->modelName;
			$modelPkey = $info->modelId;
			if ($modelName) {
				$depends[$field] = [$modelName, $modelPkey];
			}
		}
		return $depends;
	}

	// プロパティのセット/取得 ==========================

	public function setPkey(?int $val) {
		$this->pkey = $val;
	}
	public function getPkey(): ?int {
		return $this->pkey;
	}
	private ?int $pkey = 0;

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

	// 活性化/不活性化 ==========================

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
			// モデルをロードする
			$logic->load($this->activate());
		}
		return $this;
	}

	/**
	 * モデルのロジックを取得する
	 * @return Logic ロジック
	 */
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
