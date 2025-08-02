<?php

namespace agileFW\app\logic;

use agileFW\base\logic\Logic;
use agileFW\base\Data\db\Query;
use agileFW\base\Data\db\Op;
use agileFW\base\data\model\Model;
use agileFW\base\data\model\Field;
use agileFW\app\data\model\User;
use agileFW\app\logic\UserOrgLogic;

class UserLogic extends Logic {
    /**
     * {@inheritdoc}
     */
    public function logicName(): string {
        return __CLASS__;
    }

    /**
     * {@inheritdoc}
     */
    public function tableName(): string {
        return 't_user';
    }

    /**
     * {@inheritdoc}
     */
    public function newModel(?int $pkey = 0): Model {
        return $this->register(new User($pkey));
    }

    /**
     * 主キーでユーザを取得する
     * @param int $pkey 主キー
     * @return ?User ユーザモデル null:見つからなかった場合
     */
    public function get(int $pkey): ?User {
        return $this->getModel($pkey);
    }

    /** @var array ユーザ名でユーザを取得するクエリ定義 */
    private const array Q_GET_USER_BY_NAME = [
        [User::name, Op::EQ,],
    ];
    /**
     * ユーザ名でユーザを取得する
     * @param string $name ユーザ名
     * @return ?User ユーザモデル
     */
    public function getByName(string $name): ?User {
        $query = $this->getQuery(self::Q_GET_USER_BY_NAME);
        $params = [':name' => $name];
        $rows = $this->select($query, $params);
        $user = $rows[0] ?? null;
        return $user;
    }
}
new UserLogic(); // ロジックの初期化
// これにより、UserLogicが登録され、他の部分で利用可能になる