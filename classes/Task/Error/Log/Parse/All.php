<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Parses all the existing error logs for the error log admin.
 * Will look for error logs in the directory returned by `Error::error_log_dir()`
 * typically `ABS_ROOT/logs/errors/`.
 * The default is to delete the files once they've been parsed.
 * To skip deleting the files, set the `delete_files` parameter to `0`.
 *
 * Examples
 *
 *     ./minion error:log:parse:all
 *     ./minion error:log:parse:all --delete_files=0
 *     // or
 *     php index.php --task=error:log:parse:all
 *     php index.php --task=error:log:parse:all --delete_files=0
 *
 * @package    XM
 * @category   Errors
 * @author     XM Media Inc.
 * @copyright  (c) 2014 XM Media Inc.
 */
class Task_Error_Log_Parse_All extends XM_Task_Error_Log_Parse_All {}