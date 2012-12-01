<?php
$reqVar = '_' . $_SERVER['REQUEST_METHOD'];
$form_vars = $$reqVar;
$bugid = $form_vars['bugid'] ;
$projectid = $form_vars['projectid'] ;
$userid = $AppUI->user_id;

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
$sql .= $bugid;

$oktask = db_exec($sql) ;
$row1=db_fetch_row($oktask);
$summary		= $row1['summary'];
$description	= $row1['description'];
$description	.= " ";
$description	.= $row1['additional_information'];

// connect again to W2P  database
db_connect( $w2Pconfig['dbhost'], $w2Pconfig['dbname'],$w2Pconfig['dbuser'], $w2Pconfig['dbpass'], $w2Pconfig['dbpersist'] );

$summary .= " ( ";
$summary .= $bugid;
$summary .= " )";

// now create the task 
$sql = "insert into tasks (task_project,task_name,task_description,task_owner,task_created,task_updated) values ($projectid,'$summary','$description',$userid,NOW(),NOW())";
$oktask = db_exec($sql) ;
$taskid = mysql_insert_id();
$sql = "update tasks set task_parent=$taskid where task_id=$taskid";
$oktask = db_exec($sql) ;
$link = "m=tasks&a=addedit&task_id=";
$link .= $taskid ;
$AppUI->redirect("$link");