<?php

namespace Nnk2\Base\validate\model;

require_once __DIR__ . '/autoload.php';

use Nnk2\Base\Util\Results;
use Nnk2\Base\data\model\Model;


/**
 * モデル単位のバリデーションの基底クラス  
 * このクラスを継承して、各モデルのバリデーションを実装する。  
 * バリデーションは、モデルのフィールドの値を検証するために使用される。
 * ```php
 * $results = ValUser::start($user)->update()->end();
 */
abstract class ValModel {
    /**
     * バリデーションのインスタンスを取得する
     * @param ValModel &$self バリデーションのインスタンス
     * @param Model $model 対象のモデル
     * @param Results $results 処理結果保持
     * @return ValModel バリデーションのインスタンス
     */
    protected static function startBase(ValModel &$self, Model|array $model, Results $results): ValModel {
        if (!$self) {
            $self = new static();
        }
        self::$self->model = $model;
        self::$self->results = $results;
        return self::$self;
    }
    private static ?ValModel $self = null;
    protected Model|array $model;
    protected Results $results;

    /**
     * コンストラクタ
     */
    private function __construct() {
    }

    /**
     * モデル作成時の値の検証を行う
     * @return ValModel 検証インスタンス
     */
    abstract public function create(): ValModel;

    /**
     * モデル検索時の値の検証を行う
     * @return ValModel 検証インスタンス
     */
    abstract public function select(): ValModel;

    /**
     * モデル更新時の値の検証を行う
     * @return ValModel 検証インスタンス
     */
    abstract public function update(): ValModel;

    /**
     * バリデーションの結果を返す
     * @return Results バリデーションの結果
     */
    public function end(): Results {
        return $this->results;
    }
}
