<?php
/*********************************************************************************
 *  PRUNE_DATABASE and clear tables
 ********************************************************************************/
/**
 * Set up an array of Jobs with the appropriate metadata
 * 'jobName' => array (
 *         'X' => 'name',
 * )
 * 'X' should be an increment of 1
 * 'name' should be the EXACT name of your function
 *
 * Your function should not be passed any parameters
 * Always  return a Boolean. If it does not the Job will not terminate itself
 * after completion, and the webserver will be forced to time-out that Job instance.
 * DO NOT USE sugar_cleanup(); in your function flow or includes.  this will
 * break Schedulers.  That function is called at the foot of cron.php
 *
 */

/**
 * This array provides the Schedulers admin interface with values for its "Job"
 * dropdown menu.
 */

$func = 'pruneDatabaseClear';
$job_strings[] = $func;
$mod_strings['LBL_'.strtoupper($func)] = $func;

/**
 * This is the code for the "Job"
 */
function pruneDatabaseClear() {
//  2020-11-05
	$GLOBALS['log']->info('----->Scheduler fired job of type pruneDatabaseClear()');

	$db = DBManagerFactory::getInstance();
	$tables = $db->getTablesArray();

	if(!empty($tables)) {
			
		foreach($tables as $kTable => $table) {
			// find tables with deleted=1
			$columns = $db->get_columns($table);
			// no deleted - won't delete
			if(empty($columns['deleted'])) continue;

			$custom_columns = array();
			if(array_search($table.'_cstm', $tables)) {
			    $custom_columns = $db->get_columns($table.'_cstm');
// 	$GLOBALS['log']->fatal('purgecc: '.print_r($custom_columns,true));
			}

			if(!empty($custom_columns) && !empty($custom_columns['id_c'])) {
				$db->query('DELETE FROM '.$table.'_cstm WHERE id_c in (SELECT id FROM '.$table.' WHERE deleted = 1)');
// 	$GLOBALS['log']->fatal('purge: '.'DELETE FROM '.$table.'_cstm WHERE id_c in (SELECT id FROM '.$table.' WHERE deleted = 1)');
            }

			// now do the actual delete
			$db->query('DELETE FROM '.$table.' WHERE deleted = 1');
// 	$GLOBALS['log']->fatal('purge: '.'DELETE FROM '.$table.' WHERE deleted = 1');
			
		} // foreach() tables

		$GLOBALS['log']->info('----->Scheduler job of type pruneDatabaseClear() ended');

		return true;
	}
	
	$GLOBALS['log']->info('----->Scheduler job of type pruneDatabaseClear() found no tables');
	return false;
}

?>
