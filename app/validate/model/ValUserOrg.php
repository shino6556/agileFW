<?php

namespace agileFW\app\validate\model;

require_once __DIR__ . '/autoload.php';

use agileFW\base\util\Results;
use agileFW\base\validate\model\ValModel;
use agileFW\base\validate\field\ValJpName;
use agileFW\base\validate\field\ValEmail;
use agileFW\app\data\model\UserOrg;

/**
 * ユーザ組織モデル単位のバリデーションのクラス  
 */
class ValUserOrg extends ValModel {
    /**
     * バリデーションのインスタンスを取得する
     * @param UserOrg|array $user 対象のユーザ
     * @param ?array $row 対象の配列
     * @param Results $results エラーを保持
     * @return ValUserOrg バリデーションのインスタンス
     */
    public static function start(UserOrg $user, ?array $row, Results $results): ValUserOrg {
        $ValUserOrg = self::startBase(self::$self, $user, $row, $results);
        return $ValUserOrg;
    }
    private static ?ValUserOrg $self = null;

    /**
     * コンストラクタ
     */
    private function __construct() {
    }

    /**
     * @inheritDoc
     */
    public function create(): ValUserOrg {
        $model = UserOrg::cast($this->model);
        $res = $this->results;
        ValJpName::start($model, null, UserOrg::name,    $res)->check(true)->end();
        ValEmail::start($model,  null, UserOrg::email,   $res)->check(true)->end();
        ValJpName::start($model, null, UserOrg::address, $res)->check(true)->end();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function select(): ValUserOrg {
        $model = UserOrg::cast($this->model);
        $row = $this->row;
        $res = $this->results;
        ValJpName::start($model, $row, UserOrg::name,    $res)->check(true)->end();
        ValEmail::start($model,  $row, UserOrg::email,   $res)->check(true)->end();
        ValJpName::start($model, $row, UserOrg::address, $res)->check(true)->end();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function update(): ValUserOrg {
        $model = UserOrg::cast($this->model);
        $res = $this->results;
        ValJpName::start($model, null, UserOrg::name,    $res)->check(true)->end();
        ValEmail::start($model,  null, UserOrg::email,   $res)->check(true)->end();
        ValJpName::start($model, null, UserOrg::address, $res)->check(true)->end();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function end(): Results {
        return $this->results;
    }
}
