<?php
/*********************************************************************************
 PRUNE deleted records with custom fields to .sql file
 ********************************************************************************/

$manifest = array (
  'acceptable_sugar_versions' => array ('6.*.*','7.*.*','8.*.*','9.*.*','10.*.*'),
  'acceptable_sugar_flavors' => array ('CE','PRO','CORP','ENT','ULT'),
  'readme' => 'README.TXT',
  'key' => '',
  'author' => 'kuske',
  'description' => '',
  'icon' => '',
  'is_uninstallable' => true,
  'name' => 'new_prune_database_clear',
  'published_date' => '2020-11-05 00:00:05',
  'type' => 'module',
  'version' => '2020-0005',
  'remove_tables' => false,
);

$installdefs = array (
  'id' => 'new_prune_database_clear',
  'language' => 
  array (
  ),
  'copy' => 
  array (
    0 => 
    array (
      'from' => '<basepath>/SugarModules/modules/Schedulers/new_prune_database_clear.php',
      'to' => 'custom/Extension/modules/Schedulers/Ext/ScheduledTasks/new_prune_database_clear.php',
    ),
  ),
);

?>