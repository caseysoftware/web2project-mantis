<?php
$reqVar = '_' . $_SERVER['REQUEST_METHOD'];
$form_vars = $$reqVar;

$bug_id = (int) w2PgetParam($_REQUEST, 'bugid', 0);
$project_id = (int) w2PgetParam($_REQUEST, 'projectid', 0);

// get the data from mantis
db_connect( $w2Pconfig['mantis_dbhost'], $w2Pconfig['mantis_dbname'],$w2Pconfig['mantis_dbuser'], $w2Pconfig['mantis_dbpass'], $w2Pconfig['dbpersist'] );
// get the definitions
$prefix =  w2PgetConfig( 'mantis_w2p_pref') ;
$mantisprefix =  w2PgetConfig( 'mantis_prefix') ;
$bug_table = $mantisprefix ;
$bug_table .= '_bug_table'  ;
$text_table = $mantisprefix ;
$text_table .= '_bug_text_table'  ;

$sql = "select summary,description,additional_information from $bug_table,$text_table where $bug_table.bug_text_id=$text_table.id and $bug_table.id =";
$sql .= $bug_id;

$oktask = db_exec($sql) ;
$row1=db_fetch_row($oktask);
$summary		= $row1['summary'];
$description	= $row1['description'];
$description	.= " ";
$description	.= $row1['additional_information'];

// connect again to W2P  database
db_connect( $w2Pconfig['dbhost'], $w2Pconfig['dbname'],$w2Pconfig['dbuser'], $w2Pconfig['dbpass'], $w2Pconfig['dbpersist'] );

$summary .= " ( $bug_id )";

// now create the task
$task = new CTask();
$task->task_name = $summary;
$task->task_project = $project_id;
$task->task_description = $description;
$task->task_owner = $AppUI->user_id;
$task->store();

$link = "m=tasks&a=addedit&task_id=" . $task->task_id;
$AppUI->redirect("$link");