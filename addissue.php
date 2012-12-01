<?php
$reqVar = '_' . $_SERVER['REQUEST_METHOD'];
$form_vars = $$reqVar;

$action = @$_REQUEST["action"];
if($action) {
	$issue_summary = w2PgetParam($_POST, 'issue_summary', '');
	$issue_description = w2PgetParam($_POST, 'issue_description', '');
	$idproj = w2PgetParam($_POST, 'issue_project', '');
	$userid = $AppUI->user_id;

		
	if( $action == 'add' ) {
		// first retrieve username
		$query1= "SELECT user_username FROM users WHERE user_id = '$userid' " ;
		$result1 = mysql_query( $query1 )or die(mysql_error());
		while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
			$username = $row1[0];
		}
		$query1= "SELECT contact_email FROM contacts WHERE contact_id = '$userid' " ;
		$result1 = mysql_query( $query1 )or die(mysql_error());
		while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
			$email= $row1[0];
		}
		// next retrieve projectname
		$query2= "SELECT project_name FROM projects WHERE project_id = '$idproj' " ;
		$result2 = mysql_query( $query2 )or die(mysql_error());
		while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
			$projname = $row2[0];
		}
		// first connect to the correct database
		db_connect( $w2Pconfig['mantis_dbhost'], $w2Pconfig['mantis_dbname'],$w2Pconfig['mantis_dbuser'], $w2Pconfig['mantis_dbpass'], $w2Pconfig['dbpersist'] );
		// get the definitions
		$prefix =  w2PgetConfig( 'mantis_w2p_pref') ;
		$mantisprefix =  w2PgetConfig( 'mantis_prefix') ;
		$mantis_bug_table = $mantisprefix ;
		$mantis_bug_table .= '_bug_table'  ;
		$mantis_bug_text_table = $mantisprefix ;
		$mantis_bug_text_table .= '_bug_text_table'  ;
		$mantis_user_table = $mantisprefix ;
		$mantis_user_table .= '_user_table'  ;
		$mantis_project_table = $mantisprefix ;
		$mantis_project_table .= '_project_table'  ;
		$mantislink =  w2PgetConfig( 'mantis_link') ;
		// next get the mantis project id based upon the name of project or task
		if ($mantislink == "A"){
			$proj  = $prefix ;
			$proj .= $idproj ;
			$proj .= ' '  ;
			$proj .= $projname ;
		} else {
			// connect to the DP database
			db_connect( $w2Pconfig['dbhost'], $w2Pconfig['dbname'],$w2Pconfig['dbuser'], $w2Pconfig['dbpass'], $w2Pconfig['dbpersist'] );
			$query3="select value_charvalue from custom_fields_values,custom_fields_struct where value_object_id=$idproj and value_field_id=field_id and field_name='Mantis' ";
			$result3 = mysql_query( $query3 )or die(mysql_error());
			//  connect to the mantis database
			db_connect( $w2Pconfig['mantis_dbhost'], $w2Pconfig['mantis_dbname'],$w2Pconfig['mantis_dbuser'], $w2Pconfig['mantis_dbpass'], $w2Pconfig['dbpersist'] );
			while ($row3 = mysql_fetch_array($result3, MYSQL_NUM)) {
				$proj = $row3[0] ;
			}
			if (!$proj){
				$proj = $projname;
			}
		}
		// verify if the project exists, if not add
		$query1= "SELECT id FROM $mantis_project_table WHERE name like '$proj' " ;
		$idprj=0;
		$result1 = mysql_query( $query1 )or die(mysql_error());
		while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
			$idprj = $row1[0];
		}
		if ($idprj==0){
			$add1 = "insert into $mantis_project_table (name) values('$proj')";
			$adding1 = mysql_query( $add1 )or die(mysql_error());
			$idprj = mysql_insert_id();
		}
		// verify if the user exists, if not add
		$query2= "SELECT id FROM $mantis_user_table WHERE username = '$username' " ;
		$iduser=0;
		$result2 = mysql_query( $query2 )or die(mysql_error());
		while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
			$iduser = $row2[0];
		}
		if ($iduser==0){
			$pwd=mantispass();
			$pwd1=$pwd;
			if (w2PgetConfig( 'mantis_login')==MD5) {
				$pwd1=md5($pwd);
			}
			$add2 = "insert into $mantis_user_table (username, password,email) values('$username','$pwd','$email')";
			$adding2 = mysql_query( $add2 )or die(mysql_error());
			$iduser = mysql_insert_id();
			if ( w2PgetConfig( 'mantis_mail')==ON) {
				$tekst="Your password for this Mantis site  ";
				$tekst .= " is : ";
				$tekst .= $pwd ;
				$subject = "Password for the Mantis site, user ";
				$subject .= $username ;
				mail($email, $subject, $tekst);
			}
		}

		// insert the issue
		$basenow	= time(true);
		$add3 = "insert into $mantis_bug_table (project_id, reporter_id, summary,date_submitted,last_updated,due_date) values('$idprj','$iduser','$issue_summary',$basenow,$basenow,$basenow)";
		$adding3 = mysql_query( $add3 )or die(mysql_error());
		$idbug = mysql_insert_id();

		// update the issue
		$updat1 = "update $mantis_bug_table set bug_text_id='$idbug' where id='$idbug'";
		$updating1 = mysql_query( $updat1 )or die(mysql_error());

		// insert the text
		$add4 = "insert into $mantis_bug_text_table (id, description) values('$idbug','$issue_description')";
		$adding4 = mysql_query( $add4 )or die(mysql_error());

		// connect again to W2P  database
		db_connect( $w2Pconfig['dbhost'], $w2Pconfig['dbname'],$w2Pconfig['dbuser'], $w2Pconfig['dbpass'], $w2Pconfig['dbpersist'] );

	}
	if ($idproj==0){
		$link= "m=projects&a=view" ;
	}else{
		$link= "m=projects&a=view&project_id=" ;
		$link .= $idproj ;
	}	
	$AppUI->redirect("$link");
}else {
	$projectid = $form_vars['projectid'] ;
}

function mantispass($len = "6"){
	$pass = NULL;
	for($i=0; $i<$len; $i++) {
		$char = chr(rand(48,122));
		while (!ereg("[a-zA-Z0-9]", $char)){
			if($char == $lchar) continue;
				$char = chr(rand(48,90));
			}
			$pass .= $char;
			$lchar = $char;
		}
	return $pass;
	}

?>

<form name="AddEdit" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="1">
<input name="action" type="hidden" value="add" >
<input name="issue_project" type="hidden" value=<?php echo "$projectid";?> >

<tr>
	<td><img src="./modules/mantis/images/mantis_logo_button.gif" alt="" border="0"></td>
	<td align="left" nowrap="nowrap" width="100%"><h1><?php echo $AppUI->_( 'New Issue' );?></h1></td>
</tr>
</table>



<table border="1" cellpadding="4" cellspacing="0" width="98%" class="std">

<?PHP if ($projectid== 0){ ?>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Project' );?>:</td>
	<td width="60%">
<?php
// pull the projects list
$q = new DBQuery;
$q->addTable('projects');
$q->addQuery('project_id, project_name');
$q->addOrder('project_name');
$projects = arrayMerge( array( 0 => '('.$AppUI->_('any', UI_OUTPUT_RAW).')' ), $q->loadHashList() );
echo arraySelect( $projects, 'issue_project', 'class="text"', $issue_project );
?>
	</td>
</tr> 
<?php }?>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Summary' );?>:</td>
	<td width="80%">
		<input name="issue_summary" class="text" size="128" ></input>
	</td>
</tr>

<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Description' );?>:</td>
	<td width="80%">
		<textarea name="issue_description" class="textarea" cols="128" rows="5" wrap="virtual"></textarea>
	</td>
</tr>



<table border="0" cellspacing="0" cellpadding="3" width="98%">
<tr>
	<td height="40" width="30%">&nbsp;</td>
	<td  height="40" width="35%" align="right">
		<table>
		<tr>
			<td>
			<input class="button" type="button" name="cancel" value="<?php echo $AppUI->_('cancel'); ?>" onClick="javascript:if(confirm('Are you sure you want to cancel.', UI_OUTPUT_JS)){location.href = '?<?php echo $AppUI->getPlace();?>';}">
			</td>
			<td>
				<input class="button" type="button" name="btnFuseAction" value="<?php echo $AppUI->_('save'); ?>" onClick="submit()">
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>

</table>
</form>
</body>
</html>