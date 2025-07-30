<?php
require_once __DIR__ . '/autoload.php';

use \DateTime as DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

const NL = "\n";

$excel = new Excel();
// for ($row = 0; $row <= 100; $row++) {
// for ($col = 0; $col <= 26 * 26; $col++) {
// $excel->set($row, $col, $row . ':' . $col);
// }
// }
// $excel->save('/tmp/hello.xlsx');
$excel->load('/tmp/sample.xlsx');
echo $excel->get(0, 0) . NL;
echo $excel->getInt(0, 1) . NL;
echo $excel->getFloat(0, 2) . NL;
echo $excel->getDate(0, 3)->format('Y-m-d H:i:s') . NL;
// var_dump($excel->getRow(0));
// var_dump($excel->getRow(0, false));
// var_dump($excel->getRowWithStyle(0));
$excel->duplicateRow(1);
$excel->clearRow(2); // 行をクリア
$excel->save('/tmp/sample.xlsx');

/**
 * Excelクラス
 * 
 * このクラスは、Excelのセル位置を計算し、値の設定と取得を行います。
 */
class Excel {
    /**
     * コンストラクタ
     * 
     * SpreadsheetオブジェクトとWorksheetオブジェクトを初期化します。
     */
    public function __construct() {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }
    private Spreadsheet $spreadsheet;
    private Worksheet $sheet;

    /**
     * セル位置を取得するメソッド
     * 
     * @param int $row 行番号
     * @param int $a_col 列番号
     * @return string セル位置（例: A1, B2など）
     */
    public static function getPos(int $row, int $a_col): string {
        $col = $a_col;
        $x = $col % 26;
        $pos = chr($x + 65);
        while ($col >= 26) {
            $col = floor($col / 26) - 1;
            $x = $col;
            $pos .= chr($x + 65);
        }
        $pos = strrev($pos);
        $pos .= $row + 1;
        return $pos;
    }

    public function getRow(int $row, bool $onlyVal = true): mixed {
        $range = $this->rowRangeStr();
        if ($onlyVal) {
            // 値のみ取得、書式は反映しない
            $values = $this->sheet->rangeToArray($range, null, true, true, true);
        } else {
            // 式そのまま、書式反映
            $values = $this->sheet->rangeToArray($range, null, false, true, true);
        }
        $rowValues = $values[$row + 1]; // 1始まりの行番号に対応
        return $rowValues ?? [];
    }
    /**
     * セルに値を設定するメソッド
     * 
     * @param int $row 行番号
     * @param int $col 列番号
     * @param mixed $value 設定する値
     * @return Excel 自身のインスタンス
     */
    public function set(int $row, int $col, mixed $value): Excel {
        $pos = self::getPos($row, $col);
        $this->sheet->setCellValue($pos, $value);
        return $this;
    }
    /**
     * セルから値を取得するメソッド
     * 
     * @param int $row 行番号
     * @param int $col 列番号
     * @return mixed セルの値
     */
    public function get(int $row, int $col): mixed {
        $pos = self::getPos($row, $col);
        $val = $this->sheet->getCell($pos)->getValue();
        return $val;
    }
    /**
     * セルから整数値を取得するメソッド
     * 
     * @param int $row 行番号
     * @param int $col 列番号
     * @return int セルの整数値
     */
    public function getInt(int $row, int $col): int {
        $val = $this->get($row, $col);
        return intval($val);
    }
    /**
     * セルから浮動小数点数を取得するメソッド
     * 
     * @param int $row 行番号
     * @param int $col 列番号
     * @return float セルの浮動小数点数
     */
    public function getFloat(int $row, int $col): float {
        $val = $this->get($row, $col);
        return floatval($val);
    }
    /**
     * セルから日時を取得するメソッド
     * 
     * @param int $row 行番号
     * @param int $col 列番号
     * @return DateTime セルの日時
     */
    public function getDate(int $row, int $col): DateTime {
        $val = $this->get($row, $col);
        if (is_numeric($val)) {
            return ExcelDate::excelToDateTimeObject($val);
        }
        return new DateTime($val);
    }
    /**
     * Excelファイルを保存するメソッド
     * 
     * @param string $filename 保存するファイル名
     */
    public function save(string $filename): void {
        $writer = new Xlsx($this->spreadsheet);
        try {
            $writer->save($filename);
        } catch (WriterException $e) {
            echo 'Error saving file: ', $e->getMessage();
        }
    }
    public function load(string $filename): bool {
        try {
            $this->spreadsheet = IOFactory::load($filename);
            $this->sheet = $this->spreadsheet->getActiveSheet();
            return true;
        } catch (ReaderException $e) {
            echo 'Error loading file: ', $e->getMessage();
            return false;
        }
    }
    public function getRowWithStyle(int $row): array {
        $row++; // Excelは1始まり
        $columns = $this->rowRange();
        $result = [];
        foreach ($columns as $col) {
            $cellAddr = $col . $row;
            $cell = $this->sheet->getCell($cellAddr);
            $style = $this->sheet->getStyle($cellAddr);

            $result[$col] = [
                'value' => $cell->getValue(),
                'bgColor' => $style->getFill()->getStartColor()->getRGB(),
                'border' => [
                    'top'    => $style->getBorders()->getTop()->getBorderStyle(),
                    'bottom' => $style->getBorders()->getBottom()->getBorderStyle(),
                    'left'   => $style->getBorders()->getLeft()->getBorderStyle(),
                    'right'  => $style->getBorders()->getRight()->getBorderStyle(),
                ],
                'borderColor' => [
                    'top'    => $style->getBorders()->getTop()->getColor()->getRGB(),
                    'bottom' => $style->getBorders()->getBottom()->getColor()->getRGB(),
                    'left'   => $style->getBorders()->getLeft()->getColor()->getRGB(),
                    'right'  => $style->getBorders()->getRight()->getColor()->getRGB(),
                ],
            ];
        }
        return $result;
    }
    public function duplicateRow(int $row): void {
        $row++; // Excelは1始まり
        $columns = $this->rowRange();

        // 1行上に挿入（既存行を下にずらす）
        $this->sheet->insertNewRowBefore($row, 1);

        foreach ($columns as $col) {
            $srcAddr = $col . ($row + 1); // コピー元（元の行は1つ下に移動している）
            $dstAddr = $col . $row;       // コピー先

            $cell = $this->sheet->getCell($srcAddr);
            $style = $this->sheet->getStyle($srcAddr);

            // 式か値をコピー
            if ($cell->isFormula()) {
                $formla = $cell->getValue();
                // 数式のセル参照を更新
                $formla = preg_replace_callback('/([A-Z]+)(\d+)/', function ($matches) use ($row) {
                    $col = $matches[1];
                    $newRow = $matches[2] - 1; // 行番号を1つ上にずらす
                    return $col . $newRow;
                }, $formla);
                $this->sheet->setCellValue($dstAddr, $formla);
            } else {
                $this->sheet->setCellValue($dstAddr, $cell->getValue());
            }

            // スタイルをコピー
            $this->sheet->duplicateStyle($style, $dstAddr);
        }
    }
    public function clearRow(int $row, bool $doStyle = false): void {
        $row++; // Excelは1始まり
        $columns = $this->rowRange($row);

        foreach ($columns as $col) {
            $cellAddr = $col . $row;
            $this->sheet->setCellValue($cellAddr, null); // セルの値をクリア
            if (!$doStyle) continue; // スタイルをクリアしない場合

            $this->sheet->getStyle($cellAddr)->applyFromArray([]); // スタイルをクリア
        }
    }
    public function rowRange(): array {
        $startCell = $this->getStartCell(); // この行の開始セル
        $endCell = $this->getEndCell(); // この行の終了セル
        return range($startCell, $endCell);
    }
    public function rowRangeStr(int $row = 0): string {
        $startCell = $this->getStartCell($row); // この行の開始セル
        $endCell = $this->getEndCell($row); // この行の終了セル
        return $startCell . ':' . $endCell;
    }
    protected function getStartCell(?int $row = null): string {
        $startCell = 'A' . $row ? $row + 1 : '';
        return $startCell;
    }
    protected function getEndCell(?int $row = null): string {
        $highestColumn = $this->sheet->getHighestColumn();
        $endCell = $highestColumn . $row ? ($row + 1) : '';
        return $endCell;
    }
}
