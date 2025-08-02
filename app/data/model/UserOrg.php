<?php

namespace agileFW\app\data\model;

require_once __DIR__ . '/autoload.php';

use agileFW\base\util\Types;
use agileFW\base\data\model\Model;
use agileFW\base\data\model\Field;
use agileFW\base\data\db\DbTypes;
use agileFW\base\logic\Logic;
use agileFW\app\logic\UserOrgLogic;

/**
 * ユーザ組織モデル  
 * ユーザが所属する組織を表すモデル。
 */
class UserOrg extends Model {
    /**
     * 組織を新規作成/取得する
     * @param int $pkey 主キー 省略 = 0 : 新規作成
     * @return UserOrg 新規作成/取得された組織モデル
     */
    public static function new(int $pkey = 0): UserOrg {
        return Model::newModel(new UserOrg($pkey));
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
        return 't_user_org';
    }

    /**
     * UserOrgにキャストする
     * @param mixed $var キャストする値
     * @return ?UserOrg null:キャストできなかった場合
     */
    public static function cast(mixed $var): ?UserOrg {
        return ($var instanceof UserOrg) ? $var : null;
    }
    /**
     * @inheritDoc
     */
    public function getLogicBase(): Logic {
        return Logic::getLogic($this->modelName());
    }

    /**
     * このモデルを管理するロジックを返す
     * @return UserOrgLogic ユーザロジック
     */
    protected function getLogic(): UserOrgLogic {
        return $this->getLogicBase();
    }

    /**
     * モデルを活性化させる  
     * @param bool $isSetter true:更新された(省略 = true)
     * @return UserOrg this
     */
    protected function act(bool $isSetter = true): UserOrg {
        return $this->actBase($isSetter);
    }

    // モデルのフィールド定義 ===========================================

    /** @var string 組織名 */
    public const string name = 'name';
    /** @var string メールアドレス */
    public const string email = 'email';
    /** @var string 住所 */
    public const string address = 'address';

    /** @var array モデルの固有フィールド定義 */
    private static array $ownFields = [];

    /**
     * モデルのフィールド定義を返す
     * @return array モデルのフィールド定義
     */
    protected function ownFields(): array {
        if (empty(self::$ownFields)) {
            self::$ownFields[self::name]    = Field::new(self::name,    'name',     '組織名',         Types::STRING, DbTypes::VARCHAR, 1,  20);
            self::$ownFields[self::email]   = Field::new(self::email,   'email',    'メールアドレス', Types::STRING, DbTypes::VARCHAR, 6,  50);
            self::$ownFields[self::address] = Field::new(self::address, 'address',  '住所',           Types::STRING, DbTypes::VARCHAR, 10, 200);
        }
        return self::$ownFields;
    }


    // プロパティのセット/取得 ================================

    public function getName(): ?string {
        return $this->act()->name;
    }
    public function setName(string $val): ?UserOrg {
        $this->act(true)->name = $val;
        return $this;
    }
    private ?string $name = null;

    public function getEmail(): ?string {
        return $this->act()->email;
    }
    public function setEmail(string $val): ?UserOrg {
        $this->act(true)->email = $val;
        return $this;
    }
    private ?string $email = null;

    public function getAddress(): ?string {
        return $this->act()->address;
    }
    public function setAddress(string $val): ?UserOrg {
        $this->act(true)->address = $val;
        return $this;
    }
    private ?string $address = null;
}
