<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    XM
 * @category   Tests
 * @author
 */
class Bench_ArraySetting extends Codebench {

	public $description = 'building an array with 1 line or multiple lines';

	public $loops = 500000;

	public function bench_one_line($subject) {
		$array = array(
			'key1' => 'key_value',
			'key2' => 'key_value',
			'key3' => 'key_value',
		);

		return TRUE;
	}

	public function bench_multiple_lines($subject) {
		$array = array();
		$array['key1'] = 'key_value';
		$array['key2'] = 'key_value';
		$array['key2'] = 'key_value';

		return TRUE;
	}
}