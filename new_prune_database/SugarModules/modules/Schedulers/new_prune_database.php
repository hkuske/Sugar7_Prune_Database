<?php

/*********************************************************************************
 *  NEW_PRUNE_DATABASE
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

$func = 'NEW_prune_database';
$job_strings[] = $func;
$mod_strings['LBL_'.strtoupper($func)] = $func;

/**
 * This is the code for the "Job"
 */

function NEW_prune_database() {
//  Version: 25.03.2011
    $GLOBALS['log']->info('----->Scheduler fired job of type NEW_prune_database()');
    $backupDir    = $GLOBALS['sugar_config']['cache_dir'].'backups';
    $backupFile    = 'backup-pruneDatabase-GMT0_'.gmdate('Y_m_d-H_i_s', strtotime('now')).'.sql';

    $db = DBManagerFactory::getInstance();
    $tables = $db->getTablesArray();

    if (!empty($tables)) {

       if(!file_exists($backupDir) || !file_exists($backupDir.'/'.$backupFile)) {
           // create directory if not existent
          mkdir_recursive($backupDir, false);
       }

       $the_file = $backupDir.'/'.$backupFile;
       $the_string = "-- pruneDatabase begin\n" .
                     "-- created: " . date('Y-m-d H:i:s') . "\n";

       if( $fh = @sugar_fopen( $the_file, "w" ) ) fputs( $fh, $the_string );

       for ($run=0;$run<2;$run++)
       {
          foreach($tables as $kTable => $table)
          {
		     if ($table != 'users')
			 {
             $qDel = '';

             if (($run==0) && (substr($table,strlen($table)-5,5) == '_cstm'))
             {
                // save custom data
                $basetable = substr($table,0,strlen($table)-5);

                // hardcore cleanup: find entries in tables_cstm without tables.deleted=0
//              $qDel = 'SELECT * FROM '.$table.' WHERE not (id_c in (select id from '.$basetable.' where deleted = 0))';
                // normal cleanup: find tables_cstm with tables.deleted=1
                $qDel = 'SELECT * FROM '.$table.' WHERE id_c in (select id from '.$basetable.' where deleted = 1)';
             }
             else if (($run==1) && (substr($table,strlen($table)-5,5) != '_cstm'))
             {
                // find tables with deleted=1
                $qDel = 'SELECT * FROM '.$table.' WHERE deleted = 1';
             }

             if ($qDel != '' )
             {

                $rDel = $db->query($qDel);// OR continue; // continue if no 'deleted' column

                // make a backup INSERT query if we are deleting.
                while($aDel = $db->fetchByAssoc($rDel))
                {
                   // build column names
                   $rCols = $db->query('SHOW COLUMNS FROM '.$table);
                   $colName = array();

                   while($aCols = $db->fetchByAssoc($rCols))
                   {
                      $colName[] = $aCols['Field'];
                   }

                   $query = 'INSERT INTO '.$table.' (';
                   $values = '';
                   foreach($colName as $kC => $column)
                   {
                      $query .= $column.', ';
                      $values .= '"'.$aDel[$column].'", ';
                   }

                   $query  = substr($query, 0, (strlen($query) - 2));
                   $values = substr($values, 0, (strlen($values) - 2));
                   $query .= ') VALUES ('.str_replace("'", "&#039;", $values).');';

                   if( $fh ) fputs( $fh, $query."\n");

                   if(empty($colName)) {
                      $GLOBALS['log']->fatal('pruneDatabase() could not get the columns for table ('.$table.')');
                   }
                } // end aDel while()

                // now do the actual delete
                $db->query('DELETE'.substr($qDel,8));

             } // if ($qDel != '' )
             } // users
          } // foreach() tables

       } // for $run

       $the_string = "-- pruneDatabase end\n";
       if( $fh ) fputs( $fh, $the_string );
       fclose( $fh );
       return true;
   }
   return false;
}


?>