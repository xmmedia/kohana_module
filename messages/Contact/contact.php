<?php defined('SYSPATH') or die ('No direct script access.');

$msg_files = Kohana::find_file('messages', 'Contact', NULL, TRUE);
return Kohana::load($msg_files[0]);