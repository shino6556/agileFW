<?php

namespace agileFW\app\validate\model;

require_once __DIR__ . '/autoload.php';

use agileFW\base\util\Results;
use agileFW\base\validate\model\ValModel;
use agileFW\base\validate\field\ValEName;
use agileFW\base\validate\field\ValPassword;
use agileFW\base\validate\field\ValEmail;
use agileFW\base\validate\field\ValInt;
use agileFW\app\data\model\User;

/**
 * ユーザモデル単位のバリデーションのクラス  
 */
class ValUser extends ValModel {
    /**
     * バリデーションのインスタンスを取得する
     * @param User|array $user 対象のユーザ
     * @param ?array $row 対象の配列
     * @param Results $results エラーを保持
     * @return ValUser バリデーションのインスタンス
     */
    public static function start(User $user, ?array $row, Results $results): ValUser {
        $valUser = self::startBase(self::$self, $user, $row, $results);
        return $valUser;
    }
    private static ?ValUser $self = null;

    /**
     * コンストラクタ
     */
    private function __construct() {
    }

    /**
     * @inheritDoc
     */
    public function create(): ValUser {
        $model = User::cast($this->model);
        $res = $this->results;
        ValEName::start($model,    null, User::name,     $res)->check(true)->end();
        ValPassword::start($model, null, User::password, $res)->check(true)->end();
        ValEmail::start($model,    null, User::email,    $res)->check(true)->end();
        ValInt::start($model,      null, User::belongId, $res)->check(true)->end();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function select(): ValUser {
        $model = User::cast($this->model);
        $row = $this->row;
        $res = $this->results;
        ValEName::start($model,    $row, User::name,     $res)->check()->end();
        ValPassword::start($model, $row, User::password, $res)->check()->end();
        ValEmail::start($model,    $row, User::email,    $res)->check()->end();
        ValInt::start($model,      $row, User::belongId, $res)->check()->end();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function update(): ValUser {
        $model = User::cast($this->model);
        $res = $this->results;
        ValEName::start($model,    null, User::name,     $res)->check(true)->end();
        ValPassword::start($model, null, User::password, $res)->check(true)->end();
        ValEmail::start($model,    null, User::email,    $res)->check(true)->end();
        ValInt::start($model,      null, User::belongId, $res)->check(true)->end();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function end(): Results {
        return $this->results;
    }
}
