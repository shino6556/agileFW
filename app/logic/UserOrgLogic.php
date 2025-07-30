<?php

namespace nnk2\app\logic;

use nnk2\base\logic\Logic;
use nnk2\base\data\model\Model;
use nnk2\app\data\model\UserOrg;

class UserOrgLogic extends Logic {
    /**
     * ロジックを返す  
     * @return UserOrgLogic ロジック
     */
    public static function newLogic(): UserOrgLogic {
        return self::newLogicBase('UserOrgLogic');
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
    public function newModel(int $pkey = 0): Model {
        return new UserOrg($pkey);
    }

    public function getModel(int $pkey): ?UserOrg {
        return $this->getModelBase($pkey);
    }
}
