<?php
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly');
}
##
## Mantis integration
## (c) Copyright 2010
## Cas Nuy
## www.nuy.info
##

$AppUI->savePlace();
$titleBlock = new CTitleBlock( 'Mantis', 'mantis_logo_button.gif', $m, "$m.$a" );
$titleBlock->show();

$project_id = (int) w2PgetParam($_REQUEST, 'project_id', 0);

$user = new CUser();
$user->load($AppUI->user_id);
$username = $user->user_username;

$query2= "SELECT method_value FROM contacts_methods WHERE method_name='email_primary' and contact_id = " . $AppUI->user_id;
$result2 = mysql_query( $query2 )or die(mysql_error());
while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
	$email= $row2[0];
}

?>
<table width="100%" cellspacing="1" cellpadding="0" border="0">
<tr>
	<td align="right"><input class="button" type="button" value="<?php echo $AppUI->_('Add Issue');?>" onclick="window.location='?m=mantis&a=addissue&projectid=<?php echo $project_id?>'"></td>
</table>


<?php

function show_issue($summary,$bugid,$username,$email)
{
    $mantispath =  w2PgetConfig( 'mantis_path') ;
    $parm = $username ;
    $parm .="||";
    $parm .= $bugid;
    $parm .="||";
    $parm .= $email;
    $parm .="||";
    $parm = base64_encode($parm) ;
    $link  = "<a href=" ;
    $link .= $mantispath ;
    $link .= "/index_dp.php?parm=";
    $link .= $parm ;
    $link .= " target=_blank";
    $link .= ">";
    $link .= $summary ;
    $link .= "</a>";
    return $link;
}

function transform_issue($summary,$desc,$bugid)
{
    global $project_id;

    $link  = "<a href=" ;
    $link .= "index.php?m=mantis&a=addTask&projectid=";
    $link .= $project_id ;
    $link .= "&bugid=";
    $link .= $bugid ;
    $link .= ">";
    $link .= $bugid ;
    $link .= "</a>";
    return $link;
}

// get the definitions
$prefix =  w2PgetConfig( 'mantis_w2p_pref') ;
$mantisprefix =  w2PgetConfig( 'mantis_prefix') ;

$mantislink =  w2PgetConfig( 'mantis_link') ;
$mantistime =  w2PgetConfig( 'mantis_time') ;

$mantis_bug_table = $mantisprefix ;
$mantis_bug_table .= '_bug_table'  ;

$mantis_bug_text_table = $mantisprefix ;
$mantis_bug_text_table .= '_bug_text_table'  ;

$mantis_user_table = $mantisprefix ;
$mantis_user_table .= '_user_table'  ;

$mantis_project_table = $mantisprefix ;
$mantis_project_table .= '_project_table'  ;

// next get the mantis project id based upon the name of project or task
if ($mantislink=="A"){
	$proj =$prefix ;
	$proj .= $project_id ;
	$proj .= '%'  ;
}else{
	$query3="select value_charvalue from custom_fields_values,custom_fields_struct where value_object_id=$project_id and value_field_id=field_id and field_name='Mantis' ";
	$result3 = mysql_query( $query3 )or die(mysql_error());
	while ($row3 = mysql_fetch_array($result3, MYSQL_NUM)) {
		$proj = $row3[0] ;
	}
	if (!$proj){
        $project = new CProject();
        $project->load($project_id);
        $projname = $project->project_name;
        $proj = $project->project_name;
	}
}

$proj1 =$prefix ;
$proj1 .= '%'  ;

// first connect to the correct database
db_connect( $w2Pconfig['mantis_dbhost'], $w2Pconfig['mantis_dbname'],
	$w2Pconfig['mantis_dbuser'], $w2Pconfig['mantis_dbpass'], $w2Pconfig['dbpersist'] );


$idprj = 0;
$query1= "SELECT id FROM $mantis_project_table WHERE name LIKE '$proj' " ;
$result1 = mysql_query( $query1 )or die(mysql_error());
if ($result1){
	while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
		$idprj = $row1[0];
	}
}
// retrieve the issue header/detail/user
if ($idprj !=0 or $project_id>0){
	$query2  = "SELECT $mantis_bug_table.id,summary,description,realname,date_submitted,status " ;
	$query2 .= "FROM $mantis_bug_table,$mantis_bug_text_table,$mantis_user_table ";
	$query2 .= "WHERE $mantis_bug_table.project_id= $idprj  and " ;
	$query2 .= "$mantis_bug_table.id= $mantis_bug_text_table.id and ";
	$query2 .= "reporter_id= $mantis_user_table.id ";
}else{
	// when using custom fields ensure to generate no results
	if ($mantislink <> "A"){
		$proj1 = "no link possible ";
	}

	$query2  = "SELECT $mantis_bug_table.id,summary,$mantis_bug_text_table.description,realname,date_submitted,$mantis_bug_table.status " ;
	$query2 .= "FROM $mantis_bug_table,$mantis_bug_text_table,$mantis_user_table, $mantis_project_table ";
	$query2 .= "WHERE $mantis_bug_table.project_id=$mantis_project_table.id and $mantis_project_table.name like '$proj1'  and " ;
	$query2 .= "$mantis_bug_table.id= $mantis_bug_text_table.id and ";
	$query2 .= "reporter_id= $mantis_user_table.id order by date_submitted";
}

$result2 = mysql_query( $query2 )or die(mysql_error());
?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th width="50"><?php echo $AppUI->_('ID');?></th>
	<th width="50"><?php echo $AppUI->_('Date');?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('User');?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Status');?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Summary');?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Description');?>&nbsp;&nbsp;</th>
</tr>
<?php
if ($result2){
while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
	$bugid= trim($row2[0]);
	$bugsummary= trim($row2[1]);
	$bugdesc= trim($row2[2]);
	$buguser= trim($row2[3]);
	$bugdate= date("d-M-Y H:i:s" ,trim($row2[4]));
	$bugstat= trim($row2[5]);

	switch ($bugstat) {
	case 10:
		$status = 'New';
		$status_color = '#ffa0a0'; # red
		break;
	case 20:
		$status = 'Feedback';
		$status_color = '#ff50a8'; # purple
		break;
	case 30:
		$status = 'Acknowledged';
		$status_color = '#ffd850'; # orange
		break;
	case 40:
		$status = 'Confirmed';
		$status_color =  '#ffffb0'; # yellow
		break;
	case 50:
		$status = 'Assigned';
		$status_color = '#c8c8ff'; # blue
		break;
	case 80:
		$status = 'Resolved';
		$status_color = '#cceedd'; # buish-green
		break;
	case 90:
		$status = 'Closed';
		$status_color = '#e8e8e8'; # light gray
		break;
	}

?>

<tr>
	<td><?php echo transform_issue($bugsummary,$bugdesc,$bugid) ?></td>
	<td><?php echo $bugdate ?></td>
	<td><?php echo $buguser ?></td>
	<td><strong><b><font color=<?php echo "$status_color";?>>
	<?php echo $status ?>
	</b></strong></td>
	<td><?php echo show_issue($bugsummary,$bugid,$username,$email) ?></td>
	<td><?php echo $bugdesc ?></td>
</tr>
<?php
	}
}
// connect again to DP  database
db_connect( $w2Pconfig['dbhost'], $w2Pconfig['dbname'],
	$w2Pconfig['dbuser'], $w2Pconfig['dbpass'], $w2Pconfig['dbpersist'] );

?>
</table>