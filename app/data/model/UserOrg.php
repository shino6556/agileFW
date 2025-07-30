<?php

namespace nnk2\app\data\model;

require_once __DIR__ . '/autoload.php';

use nnk2\base\util\Types;
use nnk2\base\data\model\Model;
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
     * フィールド定義の一覧を返す
     * @return array フィールド定義の一覧
     */
    public function getOwnFields(): array {
        return self::FIELDS;
    }

    /** @var string 組織名 */
    public const string name = 'name';
    /** @var string メールアドレス */
    public const string email = 'email';
    /** @var string 住所 */
    public const string address = 'address';

    /** @var array モデルのフィールド定義 */
    private const array FIELDS = [
        self::name =>    ['name',    '組織名',         Types::STRING, DbTypes::VARCHAR, 4, 20],
        self::email =>   ['email',   'メールアドレス', Types::STRING, DbTypes::VARCHAR, 5, 50],
        self::address => ['address', '住所',           Types::STRING, DbTypes::VARCHAR, 0, 100],
    ];
    /**
     * UserOrgにキャストする
     * @param mixed $var キャストする値
     * @return ?UserOrg null:キャストできなかった場合
     */
    public static function cast(mixed $var): ?UserOrg {
        return ($var instanceof UserOrg) ? $var : null;
    }

    protected function getLogic(): UserOrgLogic {
        $logic = Logic::getLogic($this->modelName());
        return $logic;
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
