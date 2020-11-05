<?php
/*********************************************************************************
 PRUNE deleted records with custom fields to .sql file
 ********************************************************************************/

$manifest = array (
  'acceptable_sugar_versions' => array ('6.*.*','7.*.*','8.*.*','9.*.*','10.*.*'),
  'acceptable_sugar_flavors' => array ('CE','PRO','CORP','ENT','ULT'),
  'readme' => '',
  'key' => '',
  'author' => 'kuske',
  'description' => '',
  'icon' => '',
  'is_uninstallable' => true,
  'name' => 'new_prune_database',
  'published_date' => '2018-07-17 00:00:03',
  'type' => 'module',
  'version' => '2018-0003',
  'remove_tables' => false,
);

$installdefs = array (
  'id' => 'new_prune_database',
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