<?php
   /*
   Plugin Name: wpLog
   description: Custom plugin to log user activity
   Version: 1.0
   Author: Shobhit
   Author URI: https://www.linkedin.com/in/shobhitchaudhary/
   */
   
/**
 * Plugin activation hook
*/
function wpLog_activate() {
	wpLog( date('Y-m-d h:i:s'). ' - Plugin activated');
	logData();
}
register_activation_hook( __FILE__, 'wpLog_activate' );

/**
 * Plugin deactivation hook
*/
function wpLog_deactivate() {
    wpLog( date('Y-m-d h:i:s'). ' - Plugin deactivated');
	logData();	
}
register_deactivation_hook( __FILE__, 'wpLog_deactivate' );

/**
 * make and write log file
*/
function wpLog($message) { 
    if(is_array($message)) { 
        $message = json_encode($message); 
    }
	if(get_option('wpLogPath')){
		$file = fopen(get_option('wpLogPath')."wpMediaLog.log","a"); 
	}else{
		$file = fopen("../wpMediaLog.log","a"); 
	}
    fwrite($file, "\n" . $message); 
    fclose($file); 
}
/**
 * Prepare and write log data
*/
function logData(){
	$user_id = get_current_user_id();
	$userData = get_userdata($user_id);
	$userName = $userData->user_login;
	$userRole = implode(', ', $userData->roles);
	$IP = 'IP -'.getUserIP();
	
	$logUserName = 'Username - '.ucfirst($userName);
	$logUserRole = 'Role - '.ucfirst($userRole);
	
	 wpLog($logUserName);
	 wpLog($logUserRole);
	 wpLog($IP);
}

/**
 * get user IP
*/
function getUserIP(){
	if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		$ip_address = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else{
		$ip_address = $_SERVER['REMOTE_ADDR'];
	}
	return $ip_address;
}
/**
 * Create plugin setting page
*/
function wpLog_settings_menu() {
    add_menu_page( 'WP Log Settings', 'WP Log Settings', 'manage_options', 'wpLog_settings', 'wpLog_settings_page' );
}
add_action( 'admin_menu', 'wpLog_settings_menu' );

/**
 * Save log file path
*/
function wpLog_settings_page() {
	
	echo '<form name="wp_Log_setting" method="POST"> <label> Log Path </label> <input type="text" name="wp_Log_setting_input" placeholder="../" /><input type="submit" class="primary button-primary"></form>';
	
	if(get_option('wpLogPath')){
		echo '<br/>Current path -' .get_option('wpLogPath').'wpLog.log<br/><br/>';
	}else{
		echo '<br/>Current path -../wpLog.log<br/><br/>';
	}
	echo 'Example path -<br/> ../ - Root <br/> ../wp-content/ - in wp-content directory <br/> ../wp-content/themes - in wp-content/themes directory';
}

if(isset($_POST['wp_Log_setting_input'])){
	update_option('wpLogPath', $_POST['wp_Log_setting_input']);
}
?>