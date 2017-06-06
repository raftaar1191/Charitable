<?php
$_tests_dir = getenv('WP_TESTS_DIR');

// Look for a wordpress-tests-lib directory on the same level as the WordPress installation.
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

if ( ! defined( 'COOKIE_DOMAIN' ) ) {
	define( 'COOKIE_DOMAIN', false );
}

if ( !defined('COOKIEPATH') ) {
	define('COOKIEPATH', 'charitable.test' );
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../charitable.php';	

    // Remove this hook to prevent issue when bootstrap deletes all existing content.
    remove_action( 'deleted_post', array( 'Charitable_Campaign_Donations_DB', 'delete_donation_records' ) );
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Prevent output to avoid Session related warning.
ob_start();

require $_tests_dir . '/includes/bootstrap.php';

echo "Installing Charitable...\n";
activate_plugin( 'charitable/charitable.php' );
charitable()->activate();
charitable()->charitable_install();

// Print the output now.
ob_end_flush();

// Re-add this hook.
add_action( 'deleted_post', array( 'Charitable_Campaign_Donations_DB', 'delete_donation_records' ) );

update_option( 'WPLANG', 'en' );

// Don't test against UTC+0
update_option( 'timezone_string', 'Australia/Darwin' );

global $current_user;
$current_user = new WP_User(1);
$current_user->set_role('administrator');

require 'includes/charitable-testcase.php';
require 'helpers/charitable-campaign-helper.php';
require 'helpers/charitable-donation-helper.php';
require 'helpers/charitable-donor-helper.php';
