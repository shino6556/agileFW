<?php

namespace nnk2\app\logic;

use nnk2\base\logic\Logic;
use nnk2\base\Data\db\Query;
use nnk2\base\Data\db\Op;
use nnk2\base\data\model\Model;
use nnk2\app\data\model\User;
use nnk2\app\data\model\UserOrg;

class UserLogic extends Logic {
    /**
     * ロジックを返す  
     * @return UserLogic ロジック
     */
    public static function newLogic(): UserLogic {
        return self::newLogicBase('UserLogic');
    }

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
    public function newModel(int $pkey = 0): Model {
        return new User($pkey);
    }

    private const array Q_GET_USER_BY_NAME = [
        [User::name, Op::EQ,],
    ];
    /**
     * ユーザ名でユーザを取得する
     * @param string $name ユーザ名
     * @return ?User ユーザモデル
     */
    public function getUserByName(string $name): ?User {
        $query = $this->getQuery(self::Q_GET_USER_BY_NAME);
        $params = [':name' => $name];
        $rows = $this->select($query, $params);
        $user = $rows[0] ?? null;
        return $user;
    }
    protected function getQuery(array $queryDef): Query {
        $query = new Query($this->tableName(), $this->fields(), $queryDef);
        return $query;
    }

    public function getModel(int $pkey): ?User {
        return $this->getModelBase($pkey);
    }
}
