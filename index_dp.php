<?php

$reqVar = '_' . $_SERVER['REQUEST_METHOD'];
$form_vars = $$reqVar;
$parm = $form_vars['parm'] ;
$parm= base64_decode($parm) ;
$url1 = explode("||", $parm);

$f_username = $url1[0];
$f_id = $url1[1];
$email= $url1[2];

require_once( 'core.php' );

$t_user_table = db_get_table( 'mantis_user_table' );
$f_perm_login='false';

$query = "SELECT  password FROM $t_user_table WHERE username='$f_username'";
$result = db_query( $query );
$f_password = db_result( $result );

if ( auth_attempt_login( $f_username, $f_password, $f_perm_login ) ) {
	if ($f_id ==0){
		print_header_redirect( 'main_page.php' );
	} else {
		print_header_redirect( 'view.php?id='.$f_id.'' );
	}
	$t_redirect_url = 'login_cookie_test.php?return=' . $f_return;

}

$hack_pwd = ranpass() ;
if (user_create ( $f_username,"$hack_pwd", "$email", null,false,true,$f_username )) {
	if ( auth_attempt_login( $f_username, "$hack_pwd" , $f_perm_login ) ) {
		// update table with e-mail address when created an account
		$cookie= ranpass(64);
		$query = "Update $t_user_table set email='$email',cookie_string='$cookie' WHERE username='$f_username'";
		$result = db_query( $query );
		if ($f_id == 0 ) {
			print_header_redirect( 'main_page.php' );
		}else {
			print_header_redirect( 'view.php?id='.$f_id.'' );

		}
		$t_redirect_url = 'login_cookie_test.php?return=' . $f_return;
	}
}


function ranpass($len = "8"){
 $pass = NULL;
 for($i=0; $i<$len; $i++) {
   $char = chr(rand(48,122));
   while (!ereg("[a-z0-9]", $char)){
     if($char == $lchar) continue;
     $char = chr(rand(48,90));
   }
   $pass .= $char;
   $lchar = $char;
 }
 return $pass;
}