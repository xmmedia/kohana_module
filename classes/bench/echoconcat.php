<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    XM Media/Request Log
 * @category   Tests
 * @author
 */
class Bench_EchoConcat extends Codebench {

	public $description = 'echo concat methods, comma or period';

	public $loops = 5000;

	public $subjects = array('foo', 'bar', 'foo', 'bar', 'foo', 'bar', 'foo', 'bar', 'foo', 'bar', 'foo', 'bar', 'foo', 'bar', 'foo', 'bar', 'foo', 'bar', 'foo', 'bar', 'foo', 'bar');

	public function bench_echo_period($subject) {
		echo 'bar' . $subject . 'foo' . $subject . 'bar';

		return TRUE;
	}

	public function bench_echo_comma($subject) {
		echo 'bar', $subject, 'foo', $subject, 'bar';

		return TRUE;
	}
}