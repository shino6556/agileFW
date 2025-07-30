<?php

namespace nnk2\app\validate\model;

require_once __DIR__ . '/autoload.php';

use nnk2\base\util\Results;
use nnk2\base\data\model\Model;
use nnk2\base\validate\model\ValModel;
use nnk2\app\data\model\User;
use nnk2\app\validate\field\ValEName;


/**
 * ユーザモデル単位のバリデーションのクラス  
 */
class ValUser extends ValModel {
    /**
     * バリデーションのインスタンスを取得する
     * @param User|array $user 対象のユーザ
     * @param Results $results エラーを保持
     * @return ValUser バリデーションのインスタンス
     */
    public static function start(User|array $user, Results $results): ValUser {
        $valUser = self::startBase(self::$self, $user, $results);
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
        ValEName::start($model, $res, User::name)->check(true)->end();
        ValEName::start($model, $res, User::password)->check(true)->end();
        ValEName::start($model, $res, User::email)->check(true)->end();
        ValEName::start($model, $res, User::belongId)->check(true)->end();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function select(): ValUser {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function update(): ValUser {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function end(): Results {
        return $this->results;
    }
}
