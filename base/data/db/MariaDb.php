<?php

namespace Nnk2\Base\data\db;

require_once __DIR__ . '/autoload.php';

use Nnk2\Base\util\ConfigUtil;
use Nnk2\Base\util\StrUtil;
use Nnk2\Base\data\db\Dbms;
use Nnk2\Base\data\db\Query;

/**
 * MariaDB操作クラス
 */
class MariaDb extends Dbms {
	/**
	 * 自分自身のシングルトンを返す
	 */
	public static function self(): MariaDb {
		if (self::$self === null) {
			self::$self = new MariaDb();
		}
		return self::$self;
	}
	private static ?MariaDb $self = null;

	/**
	 * {@inheritDoc}
	 */
	public function className(): string {
		return __CLASS__;
	}

	/** @var string const DB接続文字列テンプレート */
	const DB_CONNECTION_STR = 'mysql:dbname={dbname};host={host};port={port};charset={charset}';

	const CNF_DBNAME  = 'DBNAME';
	const CNF_HOST    = 'HOST';
	const CNF_PORT    = 'PORT';
	const CNF_CHARSET = 'CHARSET';

	/** 
	 * @inheritDoc
	 */
	protected function getConnectionString(string $confFile = ''): string {
		if ($confFile === '') {
			$confFile = self::CONFIG_FILE;
		}
		// 設定ファイルからDB接続情報を取得
		$keyVal = [
			'dbname'  => ConfigUtil::get(self::CNF_DBNAME, $confFile),
			'host'    => ConfigUtil::get(self::CNF_HOST, $confFile),
			'port'    => ConfigUtil::get(self::CNF_PORT, $confFile),
			'charset' => ConfigUtil::get(self::CNF_CHARSET, $confFile),
		];
		$str = StrUtil::embed(self::DB_CONNECTION_STR, $keyVal);
		return $str;
	}
	/**
	 * 比較演算子の文字列表現の一覧
	 * @return array 全ての比較演算子 
	 */
	public const array OP_LIST = [
		'NOP' => "",
		'EQ' => "{0} = {1}",
		'NE' => "{0} <> {1}",
		'LT' => "{0} < {1}",
		'GT' => "{0} > {1}",
		'LE' => "{0} <= {1}",
		'GE' => "{0} >= {1}",
		'IN' => "{0} in ({1})",
		'NOT_IN' => "{0} not in ({1})",
		'BETWEEN' => '{0} between {1} and {2}',
		'STARTS' => "{0} like '{1}%'",
		'ENDS' => "{0} like '%{1}'",
		'CONTAINS' => "{0} like '%{1}%'",
		'NULL' => "{0} is null",
		'NOT_NULL' => "{0} is not null",
		'AND' => " AND ",
		'OR' => " OR ",
	];
}
// 比較演算子の文字列表現をセット
Query::setOpList(MariaDb::OP_LIST);
