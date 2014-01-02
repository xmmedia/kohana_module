<?php defined('SYSPATH') or die('No direct script access.');

class XM_Directory_Helper {
	/**
	 * Creates an array of all the files in a directory and it's sub directories.
	 * Each sub directory will have a nested array.
	 * The key and the value of any file will be the full path to the file.
	 * The key to sub directory array will be the full path the sub directory.
	 *
	 * @param  string  $directory  The directory to start with.
	 * @return  array
	 */
	public static function list_files($directory) {
		$directory .= DIRECTORY_SEPARATOR;

		$found = array();

		if (is_dir($directory)) {
			// Create a new directory iterator
			$dir = new DirectoryIterator($directory);

			foreach ($dir as $file) {
				// Get the file name
				$filename = $file->getFilename();

				if ($filename[0] === '.' || $filename[strlen($filename)-1] === '~') {
					// Skip all hidden files and UNIX backup files
					continue;
				}

				// Relative filename is the array key
				$key = $directory . $filename;

				if ($file->isDir()) {
					if ($sub_dir = Directory::list_files($key)) {
						if (isset($found[$key])) {
							// Append the sub-directory list
							$found[$key] += $sub_dir;
						} else {
							// Create a new sub-directory list
							$found[$key] = $sub_dir;
						}
					}
				} else {
					if ( ! isset($found[$key])) {
						// Add new files to the list
						$found[$key] = realpath($file->getPathName());
					}
				} // if
			} // foreach
		} // if

		return $found;
	} // function list_files_in_dir
}