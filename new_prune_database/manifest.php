<?php
/*********************************************************************************
 ********************************************************************************/

$manifest = array (
  'acceptable_sugar_versions' => array (
	'6.*,*',
	'7.*.*',
	'8.*.*',
	'9.*.*',
	'10.*.*',
	),
  'acceptable_sugar_flavors' => array (
	'CE',
	'PRO',
	'CORP',
	'ENT',
	'ULT',
	),
  'readme' => '',
  'key' => '',
  'author' => 'kuske',
  'description' => '',
  'icon' => '',
  'is_uninstallable' => true,
  'name' => 'new_prune_database_sql',
  'published_date' => '2020-11-03 03:00:00',
  'type' => 'module',
  'version' => '2020-0003',
//  'remove_tables' => 'prompt',
);

$installdefs = array (
  'id' => 'new_prune_database_sql',
  'language' => 
  array (
  ),
  'copy' => 
  array (
    0 => 
    array (
      'from' => '<basepath>/SugarModules/modules/Schedulers/new_prune_database.php',
      'to' => 'custom/Extension/modules/Schedulers/Ext/ScheduledTasks/new_prune_database.php',
    ),
  ),
);

?>