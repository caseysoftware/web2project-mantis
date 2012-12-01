<?php

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Mantis';
$config['mod_version'] = '2.2.3';
$config['mod_directory'] = 'mantis';
$config['mod_setup_class'] = 'SMantis';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Mantis';
$config['mod_ui_icon'] = '';
$config['mod_description'] = 'Issue Management';

if (@$a == 'setup') {
	echo w2PshowModuleConfig( $config );
}

class SMantis {   

	public function install() {
		return true;
	}
	
	public function remove() {
		return true;
	}
	
	public function upgrade() {
		return true;
	}
}