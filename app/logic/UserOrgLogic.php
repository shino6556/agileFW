<?php

namespace nnk2\app\logic;

use nnk2\base\logic\Logic;
use nnk2\base\data\model\Model;
use nnk2\app\data\model\UserOrg;

class UserOrgLogic extends Logic {
    /**
     * {@inheritdoc}
     */
    public static function newLogic(): UserOrgLogic {
        return self::newLogic(__CLASS__);
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
        return 't_user_org';
    }

    /**
     * {@inheritdoc}
     */
    public function newModel(?int $pkey = 0): Model {
        return $this->register(new UserOrg($pkey));
    }

    /**
     * 主キーでユーザ組織を取得する
     * @param int $pkey 主キー
     * @return ?UserOrg ユーザ組織モデル null:見つ
     */
    public function get(int $pkey): ?UserOrg {
        return $this->getModel($pkey);
    }
}
