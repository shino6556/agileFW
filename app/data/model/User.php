<?php

namespace agileFW\app\data\model;

require_once __DIR__ . '/autoload.php';

use agileFW\base\util\Types;
use agileFW\base\data\model\Model;
use agileFW\base\data\model\Field;
use agileFW\base\data\db\DbTypes;
use agileFW\base\logic\Logic;
use agileFW\app\logic\UserLogic;
use agileFW\app\data\model\UserOrg;

/**
 * ユーザモデル  
 * ユーザ情報を表すモデル。
 */
class User extends Model {
	/**
	 * ユーザを新規作成/取得する
	 * @param int $pkey 主キー 省略 = 0 : 新規作成
	 * @return User 新規作成/取得されたユーザモデル
	 */
	public static function new(int $pkey = 0): User {
		return Model::newModel(new User($pkey));
	}
	/**
	 * @inheritDoc
	 */
	public function modelName(): string {
		return __CLASS__;
	}
	/**
	 * @inheritDoc
	 */
	public function tableName(): string {
		return 't_user';
	}

	/**
	 * @inheritDoc
	 */
	public function getLogicBase(): Logic {
		return Logic::getLogic($this->modelName());
	}

	/**
	 * このモデルを管理するロジックを返す
	 * @return UserLogic ユーザロジック
	 */
	protected function getLogic(): UserLogic {
		return $this->getLogicBase();
	}

	/**
	 * Userにキャストする
	 * @param mixed $var キャストする値
	 * @return ?User null:キャストできなかった場合
	 */
	public static function cast(mixed $var): ?User {
		return ($var instanceof User) ? $var : null;
	}

	/**
	 * モデルを活性化させる  
	 * @param bool $isSetter true:更新された(省略 = true)
	 * @return User this
	 */
	protected function act(bool $isSetter = true): User {
		return $this->actBase($isSetter);
	}

	// プロパティの定義 =================================

	/** @var string ユーザ名 */
	public const string name = 'name';
	/** @var string メールアドレス */
	public const string email = 'email';
	/** @var string パスワード */
	public const string password = 'password';
	/** @var string 所属ID */
	public const string belongId = 'belongId';
	/** @var string 所属 */
	public const string belong = 'belong';

	/** @var array モデルの固有フィールド定義 */
	private static array $ownFields = [];

	/**
	 * モデルのフィールド定義を返す
	 * @return array モデルのフィールド定義
	 */
	protected function ownFields(): array {
		if (empty(self::$ownFields)) {
			self::$ownFields[self::name]     = Field::new(self::name,      'name',      'ユーザ名',       Types::STRING, DbTypes::VARCHAR, 4, 20);
			self::$ownFields[self::email]    = Field::new(self::email,     'email',     'メールアドレス', Types::STRING, DbTypes::VARCHAR, 4, 50);
			self::$ownFields[self::password] = Field::new(self::password,  'password',  'パスワード',     Types::STRING, DbTypes::VARCHAR, 4, 20);
			self::$ownFields[self::belongId] = Field::new(self::belongId,  'belong_id', '所属ID',         Types::INT,    DbTypes::BIG_INT, 1,);
			self::$ownFields[self::belong]   = Field::ref(self::belong,    'belong',    '所属',           'UserOrg',     'belongId');
		}
		return self::$ownFields;
	}

	//// プロパティのセット/取得 ////

	/**
	 * ユーザ名をセットする
	 * @param string $val ユーザ名
	 * @return ?User this
	 */
	public function setName(string $val): ?User {
		$this->act(true)->name = $val;
		return $this;
	}
	/**
	 * ユーザ名を取得する
	 * @return ?string ユーザ名
	 */
	public function getName(): ?string {
		return $this->act()->name;
	}
	private ?string $name = null;

	/**
	 * メールアドレスをセットする
	 * @param string メールアドレス
	 * @return User this
	 */
	public function setEmail(string $val): User {
		$this->act(true)->email = $val;
		return $this;
	}
	/**
	 * メールアドレスを取得する
	 * @return ?string メールアドレス
	 */
	public function getEmail(): ?string {
		return $this->act()->email;
	}
	private ?string $email = null;

	/**
	 * パスワードをセットする
	 * @param string $val パスワード
	 * @return ?User this
	 */
	public function setPassword(string $val): ?User {
		$this->act(true)->password = $val;
		return $this;
	}
	/**
	 * パスワードを取得する
	 * @return ?string パスワード
	 */
	public function getPassword(): ?string {
		return $this->act()->password;
	}
	private ?string $password = null;

	/**
	 * 所属IDをセットする
	 * @param int $val 所属ID
	 * @return ?User this
	 */
	public function setBelongId(int $val): ?User {
		$this->act(true)->belongId = $val;
		return $this;
	}
	/**
	 * 所属IDを取得する
	 * @return ?int 所属ID
	 */
	public function getBelongId(): ?int {
		return $this->act()->belongId;
	}
	private ?int $belongId = null;

	/**
	 * 所属をセットする
	 * @param UserOrg $val 所属
	 * @return ?User this
	 */
	public function setBelong(UserOrg $val): ?User {
		$this->act(true)->belong = $val;
		$this->act(true)->belongId = $val->getPkey();
		return $this;
	}
	/**
	 * 所属を取得する
	 * @return ?UserOrg 所属
	 */
	public function getBelong(): ?UserOrg {
		return $this->act()->belong;
	}
	private ?UserOrg $belong = null;
}
