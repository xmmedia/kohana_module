<?php defined('SYSPATH') or die ('No direct script access.');

class XM_XLS {
	/**
	 * Adds headings to the sheet, setting the width and bolding the text.
	 *
	 * @param  PHPExcel_Worksheet  $sheet  The worksheet to modify.
	 * @param  array  $headings  The array of headings to add.
	 * @param  int    $row_num   The row number to add the headers on.
	 */
	public static function add_headings($sheet, $headings, $row_num = 1) {
		$col = 0;
		foreach($headings as $_heading) {
			$sheet->setCellValueByColumnAndRow($col, $row_num, $_heading['name']);
			$sheet->getColumnDimensionByColumn($col)->setWidth($_heading['width']);
			++ $col;
		}

		// set all the headings to bold
		// uses the column counter from the previous foreach
		$columns = array();
		for($i = 0; $i < $col; $i ++) {
			$columns[] = XLS::number_to_excel_col($i);
		}
		foreach ($columns as $column) {
			$sheet->getStyle($column . $row_num)->getFont()->setBold(TRUE);
		}
	} // function add_headings

	/**
	 * Adds a row of data.
	 *
	 * @param  PHPExcel_Worksheet  $sheet  The worksheet to modify.
	 * @param  int  $row_num  The row number to add at.
	 * @param  array  $row  The array of data to add.
	 */
	public static function add_row($sheet, $row_num, $row) {
		$col = 0;
		foreach ($row as $col_val) {
			$sheet->setCellValueByColumnAndRow($col, $row_num, $col_val);
			++ $col;
		}
	} // function add_row

	/**
	 * Adds a row of data using setCellValueExplicit and setting the type to string.
	 *
	 * @param  PHPExcel_Worksheet  $sheet  The worksheet to modify.
	 * @param  int  $row_num  The row number to add at.
	 * @param  array  $row  The array of data to add.
	 * @param  int  $type  The value type in Excel.
	 *
	 * @see  PHPExcel_Cell_DataType
	 */
	public static function add_row_explicit($sheet, $row_num, $row, $type = NULL) {
		if ($type === NULL) {
			$type = PHPExcel_Cell_DataType::TYPE_STRING;
		}
		$col = 0;
		foreach ($row as $col_val) {
			$sheet->setCellValueExplicit(XLS::number_to_excel_col($col) . $row_num, $col_val, $type);
			++ $col;
		}
	} // function add_row_explicit

	/**
	 * Runs the related letter for the column in Excel.
	 * Columns start at 0 => A.
	 * ie, 5 => F, 159 => FD
	 *
	 * @param  int  $num  The number to convert.
	 * @return  string
	 */
	public static function number_to_excel_col($num) {
		$numeric = $num % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval($num / 26);
		if ($num2 > 0) {
			return self::number_to_excel_col($num2 - 1) . $letter;
		} else {
			return $letter;
		}
	} // function number_to_excel_col

	/**
	 * Cleans the sheet name, replacing any characters that are not allowed with spaces.
	 * Found this this list on [Stack Overflow](http://stackoverflow.com/questions/451452/valid-characters-for-excel-sheet-names).
	 *
	 * @param  string  $name  The sheet name to clean.
	 * @return  string
	 */
	public static function clean_sheet_name($name) {
		return UTF8::str_ireplace(array('[', ']', '*', '/', '\\', '?', ':'), ' ', $name);
	}
}