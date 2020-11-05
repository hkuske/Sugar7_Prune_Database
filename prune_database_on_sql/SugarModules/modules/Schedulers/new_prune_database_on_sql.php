<?php
/*********************************************************************************
 *  PRUNE_DATABASE_ON_SQL
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

$func = 'pruneDatabaseOnSql';
$job_strings[] = $func;
$mod_strings['LBL_'.strtoupper($func)] = $func;

/**
 * This is the code for the "Job"
 */
function pruneDatabaseOnSql() {
//  2015-03-23
	$GLOBALS['log']->info('----->Scheduler fired job of type pruneDatabaseOnSql()');
	$backupDir	= sugar_cached('backups');
	$backupFile	= 'backup-pruneDatabase-GMT0_'.gmdate('Y_m_d-H_i_s', strtotime('now')).'.sql';

	$db = DBManagerFactory::getInstance();
	$tables = $db->getTablesArray();

	if(!empty($tables)) {
	
		if(!file_exists($backupDir) || !file_exists($backupDir.'/'.$backupFile)) {
			// create directory if not existent
			mkdir_recursive($backupDir, false);
		}

		$the_file = $backupDir.'/'.$backupFile;
		$the_string = "-- pruneDatabase begin\n" .
                      "-- created: " . date('Y-m-d H:i:s') . "\n";
		if( $fh = @sugar_fopen( $the_file, "w" ) ) fputs( $fh, $the_string );
		else return false; // do nothing, file system is not ready
		
		foreach($tables as $kTable => $table) {
			// find tables with deleted=1
			$columns = $db->get_columns($table);
			// no deleted - won't delete
			if(empty($columns['deleted'])) continue;

			$custom_columns = array();
			if(array_search($table.'_cstm', $tables)) {
			    $custom_columns = $db->get_columns($table.'_cstm');
			    if(empty($custom_columns['id_c'])) {
			        $custom_columns = array();
			    }
			}

			$qDel = "SELECT * FROM $table WHERE deleted = 1";
			$rDel = $db->query($qDel);

			// make a backup INSERT query if we are deleting.
			while($aDel = $db->fetchByAssoc($rDel, false)) {
				// build column names

				$the_string = $db->insertParams($table, $columns, $aDel, null, false);
				if( $fh ) fputs( $fh, $the_string.";\n");

				if(!empty($custom_columns) && !empty($aDel['id'])) {
                    $qDelCstm = 'SELECT * FROM '.$table.'_cstm WHERE id_c = '.$db->quoted($aDel['id']);
                    $rDelCstm = $db->query($qDelCstm);

                    // make a backup INSERT query if we are deleting.
                    while($aDelCstm = $db->fetchByAssoc($rDelCstm)) {
                        $the_string = $db->insertParams($table.'_cstm', $custom_columns, $aDelCstm, null, false);
						if( $fh ) fputs( $fh, $the_string.";\n");
                    } // end aDel while()

                    $db->query('DELETE FROM '.$table.'_cstm WHERE id_c = '.$db->quoted($aDel['id']));
                }
			} // end aDel while()
			// now do the actual delete
			$db->query('DELETE FROM '.$table.' WHERE deleted = 1');
		} // foreach() tables

        $the_string = "-- pruneDatabase end\n";
        if( $fh ) fputs( $fh, $the_string );
        fclose( $fh );

		return true;
	}
	return false;
}

?>