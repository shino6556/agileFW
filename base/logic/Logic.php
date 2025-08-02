<?php

namespace agileFW\Base\logic;

require_once __DIR__ . '/autoload.php';

use agileFW\base\data\db\Dbms;
use agileFW\base\data\db\Query;
use agileFW\base\data\model\Model;
use agileFW\base\data\model\Field;
use agileFW\base\util\ArrayUtil;
use agileFW\base\util\DateUtil;
use agileFW\base\util\Logger;

/**
 * モデルと対に自動生成され、モデルのCRUDを提供する。  
 */
abstract class Logic {
	use Logger;

	// public static Logic $THIS; //シングルトン
	/** @var array 全ロジック */
	protected static array $allLogics = [];

	/** @var array このロジックの管理する全モデル */
	protected array $allModels = [];

	/** @var array 新規作成された全モデル */
	protected array $createdModels = [];

	/** @var array 主キーでモデルが登録済みか否か */
	protected array $exists = [];

	/** @var array 更新された全モデル */
	protected array $updatedModels = [];

	/** @var Dbms 対応するDBMS */
	protected Dbms $dbms;

	/** @var Model 管理するModel */
	protected Model $seed;

	/**
	 * コンストラクタ
	 * 自分自身をLogic::$allLogicsに登録する
	 */
	public function __construct() {
		self::$allLogics[$this->logicName()] = $this;
	}

	/**
	 * ロジック名を返す  
	 * 派生クラスで下記のように実装する
	 * ```php
	 * public function logicName(): string { return __CLASS__; }
	 * ```
	 * @return string ロジック名
	 */
	abstract public function logicName(): string;

	/**
	 * テーブル名を返す  
	 * 派生クラスで下記のように実装する
	 * ```php
	 * public function tableName(): string { return 't_table_name'; }
	 * ```
	 * @return string テーブル名
	 */
	abstract public function tableName(): string;

	/**
	 * モデルのインスタンスを新規生成する。  
	 * @param int $pkey 主キー (省略 = 0)
	 * @return Model 新規モデル
	 * 派生クラスで下記のように実装する。
	 * ```php
	 * protected function newModel(int $pkey): Model {
	 *     return $this->register($this->newUser($pkey));
	 * }
	 * public function newUser(int $pkey): User {
	 *    return $this->newModel($pkey);
	 * }
	 * ```
	 */
	abstract protected function newModel(?int $pkey = 0): Model;

	/**
	 * フィールド情報を得るため、モデルのインスタンスを返す。  
	 * @return Model
	 */
	protected function getSeed(): Model {
		if (!isset($this->seed)) {
			$this->seed = $this->newModel(null);
		}
		return $this->seed;
	}
	/**
	 * フィールド情報を返す。  
	 * @return array フィールド情報
	 */
	protected function fields(): array {
		return $this->getSeed()->fields();
	}

	private const string LOGIC_PREFIX = 'agileFW\app\logic\\';

	/**
	 * モデル名からロジック名を取得する  
	 * @param string $modelName モデル名
	 * @return string ロジック名
	 */
	public static function getLogicNameByModelName(string $modelName): string {
		$array = explode('\\', $modelName);
		$className = array_pop($array) . 'Logic';
		$logicName = self::LOGIC_PREFIX . $className;
		return $logicName;
	}
	/**
	 * モデル名からロジックを取得する  
	 * @param string $modelName モデル名
	 * @return ?Logic null:見つからなかった場合
	 */
	public static function getLogicByName(string $modelName): ?Logic {
		$logicName = self::getLogicNameByModelName($modelName);
		$logic = ArrayUtil::get(self::$allLogics, $logicName);
		if ($logic === null) {
			$logic = new $logicName();
		}
		return $logic;
	}
	/**
	 * モデル名で対応するロジックを取得する
	 * @param string $modelName モデル名
	 * @return ?Logic null:見つからなかった場合
	 */
	public static function getLogic(string $modelName): ?Logic {
		$logic = ArrayUtil::get(self::$allLogics, $modelName . 'Logic');
		if ($logic instanceof Logic === false) {
			$logic = self::getLogicByName($modelName);
		}
		return $logic;
	}

	/**
	 * モデルを$allModelsに登録する。
	 * @param Model $model 登録するモデル
	 * @param bool $updated true:更新された(省略 = false) 
	 * @return Model
	 */
	public function register(Model $model, bool $updated = false): Model {
		$pkey = $model->getPkey();
		if ($pkey === null) {
			// 主キーがnullならシード
			return $model;
		}
		if ($pkey === 0) {
			// 主キーが0なら新規登録
			$pkey = --$this->newPkey;
			$model->setPkey($pkey);
		}
		$other = ArrayUtil::get($this->allModels, $pkey);
		if ($other) {
			$model = $other; // 既に登録済みならそれを返す
		}
		if ($pkey <= 0 && isset($this->exists[$pkey]) === false) {
			// 新規登録にモデルを追加
			$this->createdModels[] = $model;
			$this->exists[$pkey] = true;
		} else if ($updated) {
			// 更新対象にモデルを追加
			$this->updatedModels[$pkey] = $model;
		}
		$this->allModels[$pkey] = $model;
		return $model;
	}
	/**
	 * 更新対象にされたモデルを削除する
	 * @param Model $model 削除するモデル
	 */
	public function removeUpdate(Model $model) {
		$pkey = $model->getPkey();
		unset($this->updatedModels[$pkey]);
	}

	/**
	 * モデルを不活性状態で取得する  
	 * 派生クラスで下記のように実装する。
	 * ```php
	 * protected function getModel(int $pkey=0): User {
	 *     return $this->getModel($pkey);
	 * }
	 * ```
	 * @param int $pkey 主キー (省略 = 0)
	 * @return Model
	 */
	public function getModel(int $pkey = 0): Model {
		$model = ArrayUtil::get($this->allModels, $pkey);
		if (!$model) {
			if ($pkey === 0) {
				$pkey = --$this->newPkey;
			}
			$model = $this->newModel($pkey);
		}
		return $model;
	}
	private int $newPkey = 0;

	/**
	 * 自動保存をキャンセルする
	 */
	public function cancel(Model $model) {
		ArrayUtil::remove($this->updatedModels, $model->getPkey());
	}

	/**
	 * 主キーにより1件のモデルをストレージからロードする
	 * @param Model $model ロードするモデル(主キーだけセットする)
	 * @param bool $lock true:更新用にロックする (省略 = false)
	 */
	public function load(Model $model, bool $lock = false): bool {
		// Modelのpkで連想配列を取得する
		$table = $this->tableName();
		$pkey = $model->getPkey();
		if ($pkey <= 0) return false;

		$types = $model->getTypes();
		// $lockなら行ロックをかける
		$values = $this->dbms->get($table, $pkey, $types, $lock);
		if (!is_array($values)) return false;

		// 取得した値をモデルにセットする
		$model->fromArray($values, true);
		// 更新用なら保存対象に登録
		if ($lock) {
			$this->updatedModels[$pkey] = $model;
		}
		return true;
	}

	/**
	 * 依存先のモデルを保存する
	 * @param Model $model 保存するモデル
	 * @return bool true:成功, false:失敗
	 */
	protected function saveDepends(Model $model): bool {
		foreach ($model->dependModel() as $field => $refModel) {
			// 依存先のモデルを取得
			$val = $model->getValue($field);
			$refModel = Model::castModel($val);
			if ($refModel === null) continue; // 依存先がない場合はスキップ

			// 依存先のモデルを保存する
			$logic = Logic::getLogic($refModel->modelName() . 'Logic');
			$logic->save();

			// 依存先の主キーをセット
			$refPkey = $model->refModelPkey($field);
			$model->setValue($refPkey, $refModel->getPkey());
		}
		return true;
	}

	/**
	 * モデルを保存する
	 * @return bool true:成功, false:失敗
	 */
	public function save(): bool {
		// 現在の主キーの最大値を得る
		$table = $this->tableName();
		$lastPkey = $this->dbms->getMax($table, 'pKey');

		// 新規登録
		list($models, $types) = $this->getValues($this->createdModels, $lastPkey);
		if ($models) {
			$ret = $this->dbms->create($table, $models, $types, true);
			if ($ret < 0) {
				return false; // 保存失敗
			}
		}
		$this->createdModels = [];
		$this->exists = [];

		// 更新
		list($models, $types) = $this->getValues($this->updatedModels);
		if ($models) {
			$ret = $this->dbms->update($table, $models, $types, true);
			if ($ret < 0) {
				return false; // 保存失敗
			}
		}
		$this->updatedModels = [];

		// 物理削除 TODO:実装

		return true;
	}

	/**
	 * 検索
	 * @param Query $query
	 * @param array $params
	 * @param bool $lock = false
	 * @return array
	 */
	public function select(Query $query, array $params, bool $lock = false): array {
		$models = [];
		$rows = $this->dbms->select($query, $params, $lock);
		if (is_array($rows)) {
			// 関連先のモデルがあれば、不活性化状態で生成	
			foreach ($rows as $model) {
				$this->setRefModel($model);
			}
		}
		return $models;
	}

	/**
	 * クエリを生成する
	 * @param array $queryDef クエリ定義
	 * @param ?array $fields フィールド情報 null:全フィールド
	 * @return Query クエリ
	 */
	protected function getQuery(array $queryDef, ?array $fields = null): Query {
		$fields = $fields ?? $this->fields();
		$query = new Query($this->tableName(), $fields, $queryDef);
		return $query;
	}

	/**
	 * モデルの値とデータ型を取得する
	 * @param array $models モデルの配列
	 * @param ?int $lastPkey 主キーの最大値 (省略 = null)
	 * @return array [値の配列, 型の配列]
	 */
	protected function getValues(array $models, ?int $lastPkey = null): array {
		$values = [];
		$types = [];
		foreach ($models as $obj) {
			$model = Model::castModel($obj);
			$now = DateUtil::now();
			// 依存対象のモデルを先に保存する
			$this->saveDepends($model);

			// 共通項目のセット
			if ($lastPkey !== null) {
				// 主キーの最大値をセットする
				$model->setPkey(++$lastPkey);
				$model->setCreateDate($now);
			}
			$model->setUpdateDate($now);
			$model->setDeleteFlag(false);

			$values = $model->toArray(null);
			if (!$types) {
				$types = $model->getTypes($values);
			}
			$values[] = $values;
		}

		return [$values, $types];
	}

	/**
	 * 関連先のモデルがあれば、不活性化状態で生成
	 * @param Model $model 取得したモデル
	 * @return void
	 */
	protected function setRefModel(Model &$model): void {
		$fields = $this->fields();
		foreach ($fields as $obj) {
			$field = Field::cast($obj);
			$refName = $field->modelName;
			if ($refName === null) continue;

			$refPkeyName = $field->modelId;
			$refPkey = $model->getValue($refPkeyName) ?? null;
			if (!$refPkey) continue;

			$refModel = new $refName($refPkey);
			$model->setValue($field->name, $refModel); // 関連先のモデルをセット
		}
	}
	/**
	 * Modelを論理削除する
	 * @param Model $model 論理削除するモデル
	 */
	public function delete(Model $model): bool {
		$model->setDeleteFlag(1);
		return true;
	}
	/**
	 * Modelの主キーで物理削除する
	 * @param Model $model 物理削除するモデル
	 */
	public function deletePhysical(Model $model): bool {
		return true;
	}
}
