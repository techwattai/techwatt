<?php
//$CURRENT_USER

//add_filter('show_admin_bar', '__return_false'); // Remove admin bar for all users

// Redirect "student" users away from admin dashboard
add_action('login_init', function () {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();        
        if (in_array('student', (array) $user->roles)) {
                wp_redirect(twUrl("PS_UDashboard"));
                exit;
        }
    }
});

// Custom Login Redirect for "student" role
add_filter('login_redirect', function ($redirect_to, $request, $user) {
    if (isset($user->roles) && in_array('student',$user->roles)) {
        return twUrl("PS_UDashboard");
    }
    //error_reporting(0);
    return admin_url();
}, 10, 3);

//Protect frontend User Inner templates pages...
add_action('template_redirect', function () {
    if (is_page('portal')) {
        // If user not logged in, redirect to login page
        if (!is_user_logged_in()) { wp_redirect(twUrl("PS_Login"));  exit; }

        // Optional: Restrict to students only
        $user = wp_get_current_user();
        if (!in_array('student', (array) $user->roles)) { wp_redirect(home_url()); exit; }
    }
}, 20);

// Track product views
function tw_track_product_views() {
    if (is_singular('product')) {
        global $post;
        $views = (int) get_post_meta($post->ID, 'views_count', true);
        $views++;
        update_post_meta($post->ID, 'views_count', $views);
    }
}
add_action('template_redirect', 'tw_track_product_views');

//////////// DATABASE CREATION ////////////
function activateDB() {
    global $wpdb;
    $charset = $wpdb->get_charset_collate(); 
    $payments = "{$wpdb->prefix}course_payments";

    $sql1 = "CREATE TABLE IF NOT EXISTS $payments (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            userid BIGINT UNSIGNED NOT NULL,
            childid VARCHAR(45) NOT NULL,
            parent_name VARCHAR(45) NULL,
            course VARCHAR(50) NULL,
            amount DECIMAL(10,2) NOT NULL,
            currency VARCHAR(10) NOT NULL DEFAULT 'GBP',
            payment_status VARCHAR(20) NOT NULL DEFAULT 'pending',
            refno VARCHAR(255) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id), KEY refno (refno)
        ) $charset;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql1);
}

register_activation_hook( PS_PLUGIN_FILE, 'activateDB' );