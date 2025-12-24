<?php
/*
Plugin Name: techwatt
Text Domain: tw
Plugin URI:  https://progmatech.com.ng/wp/plugins/?techwatt
Description: WP custom plugin built for techwatt Robotics & AI.
Version:     1.0
Author:      OYEYEMI Olatunde Francis
Author URI:  https://progmatech.com.ng
License:     GPL2
*/

error_reporting(0);
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action('init', function () {
    if (!session_id()) session_start();
    if (!empty($_GET['ref'])) {
        $_SESSION['twrefid'] = sanitize_text_field($_GET['ref']);
    }

    global $CURRENT_USER;
    $CURRENT_USER = wp_get_current_user();

}, 1);

// Hook into wp_body_open — runs immediately after <body>
add_action( 'wp_body_open', function() {
    echo '<span id="msgbox" style="position:fixed;top:11%;right:20px;z-index:100;background:#f5f5f5;padding:5px 10px;border-radius:5px;display:none;"></span>';
    echo '<div id="tw-popup-overlay" style="display:none;"><div id="tw-popup"><span class="tw-close">&times;</span><div class="tw-content"></div></div></div>';
});

define('PS_PLUGIN_FILE', __FILE__); //C:\xampp\htdocs\techwatty\wp-content\plugins\techwatt\techwatt.php (For activatn,reg,etc.)
define('PS_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); //https://yourdomain.com/wp-content/plugins/techwatt/ (For css,img,js,etc.)
define('PS_NoImage',plugins_url('assets/images/noimage.jpg', __FILE__));
define('PS_PLUGIN_PATH', plugin_dir_path(__FILE__)); //C:\xampp\htdocs\techwatty\wp-content\plugins\techwatt\ (For includes,requires,etc.)

function twStripeKeys() {
    $pk = get_option('tw_stripe_pk') ?? '';
    $sk = get_option('tw_stripe_sk') ?? '';
    return [ 'pk' => $pk, 'sk' => $sk ];
}

function twUrl($key) {
    $map = [
       'PS_Home' => '/',
       'PS_UConfirm' => '/portal?confirm',
       'PS_UDashboard' => '/portal',
       'PS_UProfile'   => '/portal?profile',
       'PS_UEditProfile' => '/portal?editprofile',
       'PS_UChangePwd' => '/portal?chgpwd',
       'PS_Quizzes' => '/portal?quizzes',
       'PS_MyReferrals' => '/portal?myreferrals',
       'PS_Testimonies' => '/portal?testimonies',       
       'PS_AddTestimony' => '/portal?add-testimony',
       'PS_BookingOrders' => '/portal?booking-order',
       'PS_AddCourse' => '/portal?add-course',
       'PS_ProductOrders' => '/portal?product-order',
       'PS_KidsProjects' => '/portal?kids-projects',
       'PS_AddProject' => '/portal?add-project',
       'PS_LMS' => 'https://lms.techwatt.com.ng/',
       'PS_LogOut' => '/logout',
       'PS_Register' => '/signup',
       'PS_TrialReg' => '/book-trial-class',
       'PS_Signup' => '/signup',
       'PS_Shop' => '/shop',
       'PS_Login' => '/signmein'
    ];
    return (strtolower($key) !== 'ps_lms') ? home_url($map[$key] ?? '/') : $map[$key];
}

// Queue Scripts for Frontend.....
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style('main-css', PS_PLUGIN_URL . 'assets/css/main.min.css', array(),'1.0.0');
    wp_enqueue_style('glow-css', PS_PLUGIN_URL . 'assets/css/glows.min.css', array(),'1.0.0');
    wp_enqueue_style('bootstrap-icons', PS_PLUGIN_URL . 'assets/bs-icons/bootstrap-icons.min.css', array(),'1.11.3');
    
    wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', [], null, true);
    wp_enqueue_script('techwatt-client-script', PS_PLUGIN_URL . 'assets/js/client-script.min.js', ['jquery'],'1.0.0', true);
    wp_localize_script('techwatt-client-script', 'TWREST', [ 'root'  => esc_url_raw(rest_url('techwatt/v1/')), 'nonce' => wp_create_nonce('wp_rest'),'stripe_pk' => twStripeKeys()['pk'] ]);
});

// Queue Scripts for Backend.....
add_action('admin_enqueue_scripts', function ( $hook ) {    
    if ( strpos($hook, 'techwatt') === false ) return;

    wp_enqueue_style('bootstrap-5','https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(),'5.3.3');
    wp_enqueue_script('bootstrap-5','https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',array('jquery'), '5.3.3',true );

    wp_enqueue_style('techwatt-bootstrap-icons', PS_PLUGIN_URL . 'assets/bs-icons/bootstrap-icons.min.css', array(),'1.11.3');
    wp_enqueue_style('techwatt-admin-style', PS_PLUGIN_URL . 'assets/css/admin.min.css',[],'1.0.0');
    wp_enqueue_script('techwatt-admin-script', PS_PLUGIN_URL . 'assets/js/admin-script.min.js', ['jquery'],'1.0.0', true);
    
});

//Settings links at the admin plugins page....must be declared on this class page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=techwatt-settings') . '">Settings</a>';
    array_unshift($links, $settings_link);    
    return $links;
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

require_once "logging-codes.php";
require_once "general_init.php";

if( is_admin() ) {
    require_once "admin_init.php";
    require_once "admin_pages.php";
    require_once "admin-settings.php";
}

require_once "mailer.php";
require_once "functions.php";
require_once "restapi.php";

require_once "process.php";

require_once "frontend-shortcode.php";
require_once "testimonies-codes.php";
require_once "bootcamp-codes.php";
require_once "futureclub-codes.php";
require_once "kidproject_shortcodes.php";

require_once "account-starter.php";
