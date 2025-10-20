<?php
/*
Plugin Name: techwatt
Text Domain: tw
Plugin URI:  https://progmatech.com.ng/wp/plugins/?techwatt
Description: WP custom plugin built for techwatt.
Version:     1.0
Author:      OYEYEMI Olatunde Francis
Author URI:  https://progmatech.com.ng
License:     GPL2
*/

//error_reporting(0);
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

define('PS_PLUGIN_FILE', __FILE__ );
define('PS_NoImage',plugins_url('assets/images/noimage.jpg', __FILE__));
//define('PS_Settings', plugin_dir_path(__FILE__) . 'settings.json');
define('PS_Home', home_url());
define('PS_UDashboard', home_url('/portal'));
define('PS_UProfile', home_url('/portal?profile'));
define('PS_UEditProfile', home_url('/portal?editprofile'));
define('PS_UChangePwd', home_url('/portal?chgpwd'));
define('PS_Quizzes', home_url('/portal?quizzes'));
define('PS_Testimonies', home_url('/portal?testimonies'));
define('PS_AddTestimony', home_url('/portal?add-testimony'));
define('PS_BookingOrders', home_url('/portal?booking-order'));
define('PS_ProductOrders', home_url('/portal?product-order'));
define('PS_KidsProjects', home_url('/portal?kids-projects'));
define('PS_AddProject', home_url('/portal?add-project'));
define('PS_LMS', 'https://lms.techwatt.com.ng/');
define('PS_LogOut', home_url('/logout'));
define('PS_Register', home_url('/signup'));
define('PS_Signup', home_url('/signup'));
define('PS_Shop', home_url('/shop'));
define('PS_Login', home_url('/signmein'));

// Queue Scripts for Frontend.....
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style('main-css', plugin_dir_url( __FILE__ ) . 'assets/css/main.css', array(),'1.0.0');
    wp_enqueue_style('glow-css', plugin_dir_url( __FILE__ ) . 'assets/css/glows.css', array(),'1.0.0');
    wp_enqueue_style('bootstrap-icons', plugin_dir_url( __FILE__ ) . 'assets/bs-icons/bootstrap-icons.min.css', array(),'1.11.3');
    wp_enqueue_script('techwatt-client-script', plugin_dir_url(__FILE__) . 'assets/js/client-script.js', ['jquery'],'1.0.0', true);
});

// Queue Scripts for Backend.....
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    // Optional: limit to your plugin page only
    if ( $hook !== 'toplevel_page_techwatt-dashboard' && $hook !== 'techwatt_page_techwatt-settings' ) {
        return;
    }
    // Enqueue your stylesheet
    wp_enqueue_style('techwatt-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin.css',[],'1.0.0');
    // Enqueue JS if needed
    wp_enqueue_script('techwatt-admin-script', plugin_dir_url(__FILE__) . 'assets/js/admin-script.js', ['jquery'],'1.0.0', true);
});

//Settings links at the admin plugins page....must be declared on this class page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=techwatt-settings') . '">Settings</a>';
    array_unshift($links, $settings_link);    
    return $links;
});

add_action('init', function() {
    global $CURRENT_USER;
    $CURRENT_USER = wp_get_current_user();
});

// Register custom "student" role
register_activation_hook(PS_PLUGIN_FILE, function () {
    //remove_role('student');
    if (!get_role('student')) {
        add_role('student', 'Student', [
            'read' => true, // minimal capability
            'level_0' => true, //ensure it shows in admin roles dropdown object
        ]);
    }
});

require_once "general_init.php";
require_once "admin_init.php";
require_once "admin_pages.php";

require_once "functions.php";
require_once "kidproject_shortcodes.php";

require_once "process.php";
require_once "logout_codes.php";

require_once "shortcodes.php";
require_once "account-starter.php";

require_once "testimonies-codes.php";
