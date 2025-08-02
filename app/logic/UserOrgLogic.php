<?php

namespace agileFW\app\logic;

use agileFW\base\logic\Logic;
use agileFW\base\data\model\Model;
use agileFW\app\data\model\UserOrg;

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
new UserOrgLogic(); // ロジックの初期化
// これにより、UserOrgLogicが登録され、他の部分で利用可能になる