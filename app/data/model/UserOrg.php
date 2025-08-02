<?php

namespace nnk2\app\data\model;

require_once __DIR__ . '/autoload.php';

use nnk2\base\util\Types;
use nnk2\base\data\model\Model;
use nnk2\base\data\model\Field;
use nnk2\base\data\db\DbTypes;
use nnk2\base\logic\Logic;
use nnk2\app\logic\UserOrgLogic;

/**
 * ユーザ組織モデル  
 * ユーザが所属する組織を表すモデル。
 */
class UserOrg extends Model {
    public function __construct(int $pkey = 0) {
        $this->setPkey($pkey);
    }

    /**
     * モデル名を返す
     * @return string モデル名
     */
    public function modelName(): string {
        return __CLASS__;
    }

    /**
     * テーブル名を返す
     * @return string テーブル名
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
     * ロジックを取得する
     * @return UserOrgLogic ユーザ組織ロジック
     */
    protected function getLogic(): UserOrgLogic {
        $logic = Logic::getLogic($this->modelName());
        return $logic;
    }

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


    //// プロパティのセット/取得 ////

    public function getName(): ?string {
        return $this->name;
    }
    public function setName(string $val): ?UserOrg {
        $this->name = $val;
        return $this;
    }
    private ?string $name = null;

    public function getEmail(): ?string {
        return $this->email;
    }
    public function setEmail(string $val): ?UserOrg {
        $this->email = $val;
        return $this;
    }
    private ?string $email = null;

    public function getAddress(): ?string {
        return $this->address;
    }
    public function setAddress(string $val): ?UserOrg {
        $this->address = $val;
        return $this;
    }
    private ?string $address = null;
}
