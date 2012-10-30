<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    XM
 * @category   Tests
 * @author
 */
class Bench_RequestLog extends Codebench {

	public $description = 'Request Log speed test';

	public $loops = 100;

	public $subjects = array(
		array(
			'user_id' => 12423,
			'path' => '/sh/view/434',
			'get' => array (
				'c_ajax' => 1,
    			'form_phase' => 1,
			),
			'post' => array (
				'sh_id' => 15559,
				'c_record' => array(
				        'key1' => array(
				                '0' => array(
				                        'form_phase' => 1,
				                        'urban_flag' => 2,
				                        'consult_ownership_id' => 'none',
				                        'received_info_flag' => 1,
				                        'review_info_flag' => 1,
				                        'proxy_flag' => 2,
				                        'renter_flag' => 2,
				                        'residence_lat' => '',
				                        'residence_long' => '',
				                        'comments' => 'Nulla vitae elit libero, a pharetra augue. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				                        'issue_other' => '',
				                        'signed_flag' => 0,
				                        'reviewed_flag' => 1,
				                        'feedback_flag' => 1,
				                    ),
				            ),
				        'key2' => array(
				                '0' => array(
				                        'id' => 68403,
				                        'contact_date' => array(
				                                'date' => '2009-11-13',
				                                'hour' => 16,
				                                'min' => 35,
				                                'sec' => 00,
				                            ),
				                        'out_time' => array(
				                                'hour' => 17,
				                                'min' => 20,
				                                'sec' => 00,
				                            ),
				                        'phase_id' => 1,
				                        'user_id' => 12457,
				                        'activity_type_id' => 7,
				                        'person_id' => 43028,
				                        'summary' => '',
				                        'password' => 'password',
				                    ),
				            ),
				        'key3' => array(
				                '0' => array(
				                        'id' => 0,
				                    ),
				                '1' => array(
				                        'id' => 24128,
				                        'issue_id' => 10,
				                        'yn_flag' => 1,
				                    ),
				                '2' => array(
				                        'id' => 24136,
				                        'issue_id' => 18,
				                        'yn_flag' => 1,
				                    ),
				                '3' => array(
				                        'id' => 24134,
				                        'issue_id' => 7,
				                        'yn_flag' => 1,
				                    ),
				                '4' => array(
				                        'id' => 24135,
				                        'issue_id' => 4,
				                        'yn_flag' => 1,
				                    ),
				                '5' => array(
				                        'id' => 24127,
				                        'issue_id' => 15,
				                        'yn_flag' => 1,
				                    ),
				                '6' => array(
				                        'id' => 24129,
				                        'issue_id' => 14,
				                        'yn_flag' => 1,
				                    ),
				                '7' => array(
				                        'id' => 24130,
				                        'issue_id' => 12,
				                        'yn_flag' => 1,
				                    ),
				                '8' => array(
				                        'id' => 24131,
				                        'issue_id' => 16,
				                        'yn_flag' => 1,
				                    ),
				                '9' => array(
				                        'id' => 24132,
				                        'issue_id' => 13,
				                        'yn_flag' => 1,
				                    ),
				                '10' => array(
				                        'id' => 24133,
				                        'issue_id' => 8,
				                        'yn_flag' => 1,
				                    ),
				                '11' => array(
				                        'id' => 24137,
				                        'issue_id' => 9,
				                        'yn_flag' => 1,
				                    ),
				            ),
				        'key4' => array(
				                '0' => array(
				                        'id' => 16178,
				                        'issue_id' => 15,
				                        'concern' => 'Nulla vitae elit libero, a pharetra augue. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				                    ),
				                '1' => array(
				                        'id' => 16179,
				                        'issue_id' => 10,
				                        'concern' => 'Nulla vitae elit libero, a pharetra augue. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				                    ),
				                '2' => array(
				                        'id' => 16180,
				                        'issue_id' => 14,
				                        'concern' => 'Nulla vitae elit libero, a pharetra augue. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				                    ),
				                '3' => array(
				                        'id' => 16181,
				                        'issue_id' => 12,
				                        'concern' => 'Nulla vitae elit libero, a pharetra augue. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				                    ),
				                '4' => array(
				                        'id' => 16182,
				                        'issue_id' => 16,
				                        'concern' => 'Nulla vitae elit libero, a pharetra augue. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				                    ),
				                '5' => array(
				                        'id' => 16183,
				                        'issue_id' => 13,
				                        'concern' => 'Nulla vitae elit libero, a pharetra augue. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				                    ),
				                '6' => array(
				                        'id' => 16184,
				                        'issue_id' => 8,
				                        'concern' => 'Nulla vitae elit libero, a pharetra augue. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				                    ),
				                '7' => array(
				                        'id' => 16185,
				                        'issue_id' => 7,
				                        'concern' => 'Nulla vitae elit libero, a pharetra augue. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				                    ),

				                '8' => array(
				                        'id' => 16186,
				                        'issue_id' => 4,
				                        'concern' => 'Nulla vitae elit libero, a pharetra augue. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				                    ),
				                '9' => array(
				                        'id' => 16187,
				                        'issue_id' => 18,
				                        'concern' => 'Nulla vitae elit libero, a pharetra augue. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				                    ),
				                '10' => array(
				                        'id' => 16188,
				                        'issue_id' => 9,
				                        'concern' => 'Nulla vitae elit libero, a pharetra augue. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				                    ),
				            ),
				        'key5' => array(
				                '0' => array(
				                        'consult_reason_id' => 6,
				                    ),
				            ),
				    ),
			), // post
		),
	);

	public function bench_orm($subject) {
		ORM::factory('request_log')
			->values($subject)
			->save();

		return TRUE;
	}

	public function bench_db_json($subject) {
		$subject['get'] = json_encode($subject['get']);
		$subject['post'] = json_encode($subject['post']);

		DB::insert('request_log', array_keys($subject))
			->values($subject)
			->execute();

		return TRUE;
	}

	public function bench_db_static_keys_json($subject) {
		$subject['get'] = json_encode($subject['get']);
		$subject['post'] = json_encode($subject['post']);

		DB::insert('request_log', array('user_id', 'path', 'get', 'post'))
			->values($subject)
			->execute();

		return TRUE;
	}

	public function bench_db_serialize($subject) {
		$subject['get'] = serialize($subject['get']);
		$subject['post'] = serialize($subject['post']);

		DB::insert('request_log', array_keys($subject))
			->values($subject)
			->execute();

		return TRUE;
	}

	public function bench_db_static_keys_serialize($subject) {
		$subject['get'] = serialize($subject['get']);
		$subject['post'] = serialize($subject['post']);

		DB::insert('request_log', array('user_id', 'path', 'get', 'post'))
			->values($subject)
			->execute();

		return TRUE;
	}

	public function bench_db_sql_serialize($subject) {
		$subject['get'] = serialize($subject['get']);
		$subject['post'] = serialize($subject['post']);

		DB::query(Database::INSERT, "INSERT INTO request_log (user_id, path, get, post) VALUES (" . Database::instance()->quote($subject['user_id']) . ", " . Database::instance()->quote($subject['path']) . ", " . Database::instance()->quote($subject['get']) . ", " . Database::instance()->quote($subject['post']) . ")")
			->execute();

		return TRUE;
	}

	public function bench_db_sql_mysql_escape_serialize($subject) {
		$subject['get'] = serialize($subject['get']);
		$subject['post'] = serialize($subject['post']);

		$connection = Database::instance()->connection();

		DB::query(Database::INSERT, "INSERT INTO request_log (user_id, path, get, post) VALUES ('" . mysql_real_escape_string($subject['user_id'], $connection) . "', '" . mysql_real_escape_string($subject['path'], $connection) . "', '" . mysql_real_escape_string($subject['get'], $connection) . "', '" . mysql_real_escape_string($subject['post'], $connection) . "')", $connection)
			->execute();

		return TRUE;
	}

	public function bench_db_sql_mysql_serialize($subject) {
		$subject['get'] = serialize($subject['get']);
		$subject['post'] = serialize($subject['post']);

		mysql_query("INSERT INTO request_log (user_id, path, get, post) VALUES ('" . mysql_real_escape_string($subject['user_id']) . "', '" . mysql_real_escape_string($subject['path']) . "', '" . mysql_real_escape_string($subject['get']) . "', '" . mysql_real_escape_string($subject['post']) . "')", Database::instance()->connection());

		return TRUE;
	}

	public function bench_model($subject) {
		$_POST = $subject['post'];
		$_GET = $subject['get'];

		Model_Request_Log::store_request();

		return TRUE;
	}

	public function bench_no_model($subject) {
		$_POST = $subject['post'];
		$_GET = $subject['get'];

		$remove_keys = (array) Kohana::$config->load('request_log.remove_keys');

		$post = $_POST;
		Model_Request_Log::recursive_unset($post, $remove_keys);

		$get = $_GET;
		Model_Request_Log::recursive_unset($get, $remove_keys);

		$data = array(
			'user_id' => Auth::instance()->get_user()->id,
			'path' => Request::current()->uri(),
			'get' => json_encode($get),
			'post' => json_encode($post),
		);

		DB::insert('request_log', array_keys($data))
			->values($data)
			->execute();

		return TRUE;
	}
}