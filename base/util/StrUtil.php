<?php

namespace agileFW\Base\Util;

require_once __DIR__ . '/autoload.php';

use agileFW\Base\Util\TypeUtil;

/**
 * 文字列操作ユーティリティ
 */
class StrUtil {
	/**
	 * インスタンス化禁止
	 */
	private function __construct() {
	}

	/**
	 * テンプレート文字列に複数の値を埋め込む
	 * @param string $template 値を埋め込むテンプレート。 例: '文字列の中の{キー}を値に置き換える'
	 * @param array $keyVals [キー=>値, ...]の配列
	 * @param string $parenthesis 括弧(省略='{}')
	 * @return string 値が埋め込まれた文字列
	 */
	public static function embed(string $template, array $keyVals, string $parenthesis = '{}'): string {
		$p = mb_str_split($parenthesis);
		$str = $template;
		foreach ($keyVals as $key => $val) {
			$place = $p[0] . $key . $p[1];
			$str = self::replace($place, $val, $str);
		}
		return $str;
	}

	/**
	 * １文字目だけを大文字にする
	 * @param string $orgStr 元の文字列
	 * @return string 変換後の文字列
	 */
	public static function upper1st(string $value): string {
		$first = substr($value, 0, 1);
		$other = substr($value, 1);
		return strtoupper($first) . $other;
	}

	/**
	 * １文字目だけを小文字にする
	 * @param string $orgStr 元の文字列
	 * @return string 変換後の文字列
	 */
	public static function lower1st(string $orgStr): string {
		$first = mb_substr($orgStr, 0, 1);
		$first = mb_strtolower($first);
		$converted = $first . mb_substr($orgStr, 1);
		return $converted;
	}

	/**
	 * マルチバイト対応の文字列置換
	 * @param string $search 検索文字列
	 * @param string $replace 置換文字列
	 * @param string  $target 対象文字列
	 * @return string
	 */
	public static function replace(string $search, string $replace, string  $target): string {
		$searchLen = mb_strlen($search);
		$replaceLen = mb_strlen($replace);
		$offset = self::find($target, $search);
		while ($offset >= 0) {
			$replaceEnd = $offset + $searchLen;
			$done   = mb_substr($target, 0, $offset);
			$remain = mb_substr($target, $replaceEnd);
			$target = $done . $replace . $remain;
			$nextStart = $offset + $replaceLen;
			$offset = self::find($target, $search, $nextStart);
		}
		return $target;
	}

	/**
	 * 対象文字列から検索文字列を探し、その位置を返す。
	 * @param string $target 対象文字列
	 * @param string $search 検索文字列
	 * @param int $offset 検索開始位置(省略:0)
	 * @return int 見つかった検索文字列の位置。見つからないと-1
	 */
	public static function find(string $target, string $search, int $offset = 0): int {
		$pos = mb_strpos($target, $search, $offset);
		if ($pos === false) return -1;
		return $pos;
	}

	/**
	 * 対象文字列が検索文字列で始まるかどうか。
	 * @param string $target 対象文字列
	 * @param string $search 検索文字列
	 * @return bool true:見つかった場合
	 */
	public static function startsWith(string $target, string $search): bool {
		$pos = self::find($target, $search);
		if ($pos == 0) return true;
		return false;
	}
	/**
	 * 対象文字列が検索文字列で終わるかどうか。
	 * @param string $target 対象文字列
	 * @param string $search 検索文字列
	 * @return bool true:見つかった場合
	 */
	public static function endsWith(string $target, string $search): bool {
		$end = mb_strlen($target);
		$end -= mb_strlen($search);
		$pos = self::find($target, $search);
		if ($pos == $end) return true;
		return false;
	}

	/**
	 * UTF-8に文字エンコードを変換する
	 * @param string $value 対象文字列
	 * @param string $prevEncoding 変換前の文字エンコード(省略='SJIS')
	 * @return string 変換した文字列
	 */
	public static function toUTF8(string $value, string $prevEncoding = 'SJIS'): string {
		$result = mb_convert_encoding($value, 'UTF-8', $prevEncoding);
		return $result;
	}

	/**
	 * SHIFT-JISに文字エンコードを変換する
	 * @param string $value 対象文字列
	 * @param string $prevEncoding 変換前の文字エンコード(省略='UTF-8')
	 * @return string 変換した文字列
	 */
	public static function toSJIS(string $value, string $prevEncoding = 'UTF-8'): string {
		$result = mb_convert_encoding($value, 'SJIS', $prevEncoding);
		return $result;
	}

	/**
	 * 対象文字列に検索文字列があるかどうか。
	 * @param string $target 対象文字列
	 * @param string|array $search 検索文字列 or その配列
	 * @param bool $isAll 全部見つける 省略=false
	 * @return bool true:見つかった場合
	 */
	public static function exists(string $target, string|array $search, bool $isAll = false): bool {
		if (is_array($search)) {
			$count = 0;
			foreach ($search as $word) {
				$pos = self::find($target, $word);
				if ($pos >= 0) $count++;
			}
			return $isAll ? (count($search) == $count) : ($count > 0);
		}
		$pos = self::find($target, $search);
		if ($pos < 0) return false;
		return true;
	}

	/** @var string カンマ */
	const COMMA = ',';

	/**
	 * 文字列にする。
	 * 数値の書式 : self::COMMA 3桁区切りか、printf() の書式。
	 * 日時の書式 : 'Y-m-d'などの形式。
	 * @param mixed $value 対象の値
	 * @param ?string $format 日付・数値の書式。配列は区分記号。省略=null
	 * @return string
	 */
	public static function toString(mixed $value, ?string $format = null): string {
		$type = TypeUtil::getType($value);
		$result = '';
		switch ($type) {
			case Types::NULL: // null : 空白文字へ
				$result = '';
				break;
			case Types::BOOL:
				$result = $value ? 'true' : 'false';
				break;
			case Types::INT:
			case Types::FLOAT:
				if ($format == self::COMMA) {
					$result = number_format($value); // 三桁区切り
				} else if ($format) {
					$result = sprintf($format, $value); // 多用途
				} else {
					$result = '' . $value;
				}
				break;
			case Types::ARRAY: // 配列
				$result = ArrayUtil::toString($value, self::COMMA);
				break;
			case Types::ENUM: // 列挙型
				$result = self::enum2str($value);
				break;
			case Types::DATETIME: // 日時
				$result = DateUtil::toString($value);
				break;
			case Types::OBJECT: // オブジェクト
				$result = get_class($value);
				break;
			case Types::MODEL: // モデル
				$array = $value->toArray();
				$result = ArrayUtil::toString($array, self::COMMA);
				break;
			default:
				$result = $value;
				break;
		}
		return $result;
	}
	/**
	 * 列挙型を'クラス名::名称'に変換する
	 * @param mixed $value 列挙型
	 * @return string 変換した文字列
	 */
	public static function enum2str(mixed $value): string {
		return get_class($value) . '::' . $value->name;
	}

	//// 文字種別変換 ////

	/** @var string 半角英字へ変換 */
	public const string CNV_HAN_ALPH = 'r';
	/** @var string 半角数字へ変換 */
	public const string CNV_HAN_NUM = 'n';
	/** @var string 半角英数記号へ変換 */
	public const string CNV_HAN_ALNUM = 'a';
	/** @var string 半角カナへ変換 */
	public const string CNV_HAN_KATA = 'k';
	/** @var string 半角記号へ変換 */
	public const string CNV_HAN_SYM = '　！”＃＄％＆￥’（）－＾￥￥＠［］，．／＝～｜‘｛｝＜＞？＿';

	/** @var string 全角英字へ変換 */
	public const string CNV_ZEN_ALPH = 'R';
	/** @var string 全角数字へ変換 */
	public const string CNV_ZEN_NUM = 'N';
	/** @var string 全角英数記号へ変換 */
	public const string CNV_ZEN_ALNUM = 'A';
	/** @var string 全角カナへ変換 */
	public const string CNV_ZEN_KATA = 'KV';
	/** @var string 全角かなへ変換 */
	public const string CNV_ZEN_HIRA = 'HV';
	/** @var string 全角記号へ変換 */
	public const string CNV_ZEN_SYM  = ' !"#$%&\'()-^\\@[],./=~|`{}<>?_';

	/** @var string 半角アルファベット */
	public const string HAN_ALPH = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	/** @var string 半角数字 */
	public const string HAN_NUM = '0123456789';
	/** @var string 半角記号 */
	public const string HAN_SYM = ' !"#$%&()^\\@[]/=~|`{}<>?_\',.-';
	/** @var string 半角アルファベット数字 */
	public const string HAN_ALNUM = self::HAN_ALPH . self::HAN_NUM . self::HAN_SYM;
	/** @var string 半角アルファベット数字記号 */
	public const string HAN_ALNUMSYM = self::HAN_ALNUM . self::HAN_SYM;

	/** @var string 全角アルファベット */
	public const string ZEN_ALPH = 'ＡＢＣＤＥＦＧＨＩＪＫＬＭＮＯＰＱＲＳＴＵＶＷＸＹＺａｂｃｄｅｆｇｈｉｊｋｌｍｎｏｐｑｒｓｔｕｖｗｘｙｚ';
	/** @var string 全角数字 */
	public const string ZEN_NUM = '０１２３４５６７８９';
	/** @var string 全角記号 */
	public const string ZEN_SYM = self::CNV_HAN_SYM;

	/**
	 * 指定した文字種別を変換する。  
	 * @param string $value 変換元文字列
	 * @param string ...$convert 変換指定
	 * @return string 変換された文字列
	 */
	public static function zenHan(string $value, string ...$convert): string {
		$conv = '';
		$fromSym = '';
		$toSym = '';
		foreach ($convert as $cv) {
			if ($cv === self::CNV_ZEN_SYM) {
				$fromSym = $cv;
				$toSym = self::CNV_HAN_SYM;
			} else if ($cv === self::CNV_HAN_SYM) {
				$fromSym = $cv;
				$toSym = self::CNV_ZEN_SYM;
			} else {
				$conv .= $cv;
			}
		}
		// 半角英数カナ → 全角英数かなカナ 変換
		$value = mb_convert_kana($value, $conv);

		// 記号を変換する
		$fromSyms = mb_str_split($fromSym);
		$toSyms = mb_str_split($toSym);
		for ($i = 0; $i < count($fromSyms); $i++) {
			$from = $fromSyms[$i];
			$to = $toSyms[$i];
			$value = StrUtil::replace($from, $to, $value);
		}
		return $value;
	}

	/**
	 * 文字列が半角文字以外を含むかどうか
	 * @param string $value 対象の文字列
	 * @return bool true:半角文字
	 */
	public static function isASCII(string $value): bool {
		$values = mb_str_split($value);
		foreach ($values as $val) {
			$ord = mb_ord($val);
			if ($ord < 0x20 || $ord > 0x7E) {
				// 0x20(半角スペース)～0x7E(半角チルダ)の範囲外
				return false;
			}
		}
		return true;
	}

	/**
	 * 文字列が検索文字列を一文字でも含むかどうか。
	 * @param string $value 対象の文字列
	 * @param string $search 検索文字列
	 * @return int 見つかった数
	 */
	public static function contains(string $value, string $search): int {
		$searchs = mb_str_split($search);
		$count = 0;
		foreach ($searchs as $s) {
			if (mb_strpos($value, $s) !== false) {
				$count++;
			}
		}
		return $count;
	}
	/**
	 * 文字列が数字のみかどうか
	 * @param ?string $a_str 対象文字列
	 * @param bool $a_sign true:+-符号を含む 省略:false
	 * @return bool true:数字のみ
	 */
	public static function isIntStr(?string $a_str, bool $a_sign = false): bool {
		if ($a_str === null) return false;
		$exp = $a_sign ? '/^[+-]?\d+$/' : '/^\d+$/';
		if (preg_match($exp, $a_str)) return true;
		return false;
	}

	/**
	 * 全角を含む文字列を指定した長さに合わせるパディングを行う
	 * @param string $a_str 対象文字列
	 * @param int $a_length 文字列の長さ
	 * @param string $a_pad パディングに使う文字列 省略=' '
	 * @param int $a_padType パディング種別 STR_PAD_RIGHT,STR_PAD_LEFT,STR_PAD_BOTH 省略=STR_PAD_RIGHT 
	 * @return string パディングされた文字列
	 */
	public static function zenStrPad(string $a_str, int $a_length, string $a_pad = ' ', int $a_padType = STR_PAD_RIGHT): string {
		$len = self::zenStrlen($a_str);
		$padLen = $a_length - $len;
		if ($padLen <= 0) return $a_str;

		switch ($a_padType) {
			case STR_PAD_RIGHT:
				$output = $a_str . str_repeat($a_pad, $padLen);
				break;
			case STR_PAD_BOTH:
				$leftPad = ceil($padLen / 2);
				$rightPad = floor($padLen / 2);
				$output = str_repeat(' ', $leftPad) . $a_str . str_repeat(' ', $rightPad);
				break;
			case STR_PAD_LEFT:
				$output = str_repeat(' ', $padLen) . $a_str;
				break;
		}
		return $output;
	}

	/**
	 * 全角文字を半角の2文字分にカウントする
	 * @param string $a_str 対象文字列
	 * @return int 全角を2文字分にカウントした文字数
	 */
	public static function zenStrlen(string $a_str): int {
		$len = 0;
		foreach (mb_str_split($a_str) as $c) {
			$ord = mb_ord($c);
			$len += ($ord >= 128) ? 2 : 1;
		}
		return $len;
	}

	/**
	 * 全角文字列の一部を取得する
	 * @param string $a_str 対象文字列
	 * @param int $a_offset 開始位置
	 * @param int $a_length 取得する長さ
	 * @return string 取得した文字列
	 */
	public static function zenSubstr(string $a_str, int $a_offset, int $a_length): string {
		$l_str = mb_substr($a_str, $a_offset, $a_length);
		$l_len = mb_strlen($l_str);
		$l_diff = self::zenStrlen($l_str) - $l_len;
		$l_str = mb_substr($a_str, $a_offset, $a_length - $l_diff);
		return $l_str;
	}

	/**
	 * 行の列ごとの最大幅の配列を返す
	 * @param array $a_rows 対象の行配列
	 * @return array 列ごとの最大幅の配列
	 */
	public static function getMaxLenRow(array $a_rows): array {
		$maxRow = [];
		foreach ($a_rows as $row) {
			if (is_array($row)) {
				for ($i = 0; $i < count($row); $i++) {
					$max = ArrayUtil::get($maxRow, $i, 0);
					$len = self::zenStrLen($row[$i]);
					$maxRow[$i] = $max < $len ? $len : $max;
				}
			} else {
				$max = ArrayUtil::get($maxRow, 0, 0);
				$len = self::zenStrLen($row);
				$maxRow[0] = $max < $len ? $len : $max;
			}
		}
		return $maxRow;
	}
}
