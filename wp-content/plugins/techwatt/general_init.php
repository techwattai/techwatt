<?php
//$CURRENT_USER

//add_filter('show_admin_bar', '__return_false'); // Remove admin bar for all users

// Redirect "student" users away from admin dashboard
add_action('login_init', function () {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();        
        if (in_array('student', (array) $user->roles)) {
                wp_redirect(PS_UDashboard);
                exit;
        }
    }
});

// Custom Login Redirect for "student" role
add_filter('login_redirect', function ($redirect_to, $request, $user) {
    if (isset($user->roles) && in_array('student',$user->roles)) {
        return PS_UDashboard;
    }
    //error_reporting(0);
    return admin_url();
}, 10, 3);

//Protect frontend User Inner templates pages...
add_action('template_redirect', function () {
    if (is_page('portal')) {
        // If user not logged in, redirect to login page
        if (!is_user_logged_in()) { wp_redirect(PS_Login);  exit; }

        // Optional: Restrict to students only
        $user = wp_get_current_user();
        if (!in_array('student', (array) $user->roles)) { wp_redirect(home_url()); exit; }
    }
});

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
