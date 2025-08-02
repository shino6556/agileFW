<?php

namespace nnk2\app\logic;

use nnk2\base\logic\Logic;
use nnk2\base\Data\db\Query;
use nnk2\base\Data\db\Op;
use nnk2\base\data\model\Model;
use nnk2\base\data\model\Field;
use nnk2\app\data\model\User;
use nnk2\app\logic\UserOrgLogic;

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
