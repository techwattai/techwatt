<?php
// Add custom rewrite rules
function tw_add_rewrite_rules() {
    add_rewrite_rule('^logout/?$', 'index.php?logout=1', 'top');
    add_rewrite_rule('^signin/?$', 'index.php?signin=1', 'top');
    add_rewrite_rule('^payment-success/?$', 'index.php?payment_success=1', 'top');
}
add_action('init', 'tw_add_rewrite_rules');

// Add custom query vars
function tw_add_query_vars($vars) {
    $vars[] = 'logout';
    $vars[] = 'signin';
    $vars[] = 'payment_success';
    return $vars;
}
add_filter('query_vars', 'tw_add_query_vars');

// Flush rewrite rules on plugin activation
register_activation_hook(PS_PLUGIN_FILE, function() {
    tw_add_rewrite_rules();
    flush_rewrite_rules();
});

// Flush rewrites on deactivation
register_deactivation_hook(PS_PLUGIN_FILE, function() {
    flush_rewrite_rules();
});

// Handle LOGIN, LOGOUT, and CUSTOM 404 in one optimized redirect callback
function tw_template_redirect() {

    if (get_query_var('payment_success')) {
        require_once PS_PLUGIN_PATH . 'templates/stripe-payment-success.php';
    }

    // --- Handle /signin ---
    if (get_query_var('signin')) {
        wp_safe_redirect( wp_login_url() );
        exit;
    }

    ////////////// Handle /logout ////////////////////
    if (get_query_var('logout') || (isset($_GET['action']) && $_GET['action'] === 'logout')) {
        wp_logout();
        wp_safe_redirect(home_url());
        exit;
    }

    // --- Handle custom 404 ---
    if (is_404()) {
        status_header(404);
        nocache_headers();

        $file = plugin_dir_path(__FILE__) . 'templates/custom-404.php';

        if (file_exists($file)) {
            include $file;
        } else {
            echo "<h1>404 - Page Not Found</h1>";
        }

        exit;
    }
}
add_action('template_redirect', 'tw_template_redirect',1);
