<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    XM Media/Search ID Storage
 * @category   Tests
 * @author
 */
class Bench_SearchIdStorage extends Codebench {
	public $description = 'Tests which method is faster for storing and recalling a large list of search IDs';

	public $loops = 100;

	public function run() {
		$subject = array();
		for ($i = 0; $i <= 3000; $i ++) {
			$subject[] = mt_rand(1, 5000);
		}

		$this->subjects = array($subject);

		return parent::run();
	}

	public function bench_file_cache($subject) {
		Cache::instance('file')->set('search_ids', implode(',', $subject));
		$subject = explode(',', Cache::instance('file')->get('search_ids'));

		return TRUE;
	}

	public function bench_cache_users($subject) {
		$key = 'search_ids-' . mt_rand(1, 500);
		Cache::instance('file')->set($key, implode(',', $subject));
		$subject = explode(',', Cache::instance('file')->get($key));

		return TRUE;
	}

	public function bench_db($subject) {
		list($search_cache_id) = DB::insert('search_cache', array('data'))
			->values(array(implode(',', $subject)))
			->execute();

		$subject = explode(',', DB::select('*')
			->from('search_cache')
			->where('id', '=', $search_cache_id)
			->execute()
			->get('data', ''));

		return TRUE;
	}
}