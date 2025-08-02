<?php

namespace agileFW\Base\Util;

require_once __DIR__ . '/autoload.php';


/**
 * 処理結果情報
 */
class ResultInfo {
    /** @var int 出力レベル マイナス:エラー, 0:警告, 1以上:トレース(大きいほど詳細) */
    public int $level = 0;
    /** @var string 処理名(クラス,メソッド,フィールド) */
    public string $name = '';
    /** @var string メッセージ(何が起こったか) */
    public string $message = '';
    /** @var array パラメータ */
    public array $params = [];

    /**
     * 文字列化する
     * @return string 変換した文字列
     */
    public function toString(): string {
        if ($this->level <= Results::ERROR) {
            $output = 'ERROR';
        } else if ($this->level == Results::WARNING) {
            $output = 'WARNING';
        } else if ($this->level >= Results::TRACE) {
            $output = 'TRACE';
        }
        $output .= ': ' . $this->name . ' : ' . $this->message . ' : ' . ArrayUtil::toString($this->params);
        return $output;
    }
}
