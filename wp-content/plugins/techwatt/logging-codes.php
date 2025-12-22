<?php
if (!defined('ABSPATH')) exit;

//Config............
function tw_get_admin_slug() {
    $slug = get_option('tw_cadmin_url');
    return $slug ? sanitize_title($slug) : 'backend';
}

// REWRITE RULES.......................
add_action('init', function () {
    add_rewrite_rule('^signin/?$', 'index.php?tw_login=signin', 'top');
    add_rewrite_rule('^logout/?$', 'index.php?tw_login=logout', 'top');
    add_rewrite_rule('^payment-success/?$', 'index.php?payment_success=1', 'top');
    $admin_slug = tw_get_admin_slug();
    if($admin_slug){
        add_rewrite_rule("^{$admin_slug}/?$", 'index.php?tw_login=backend', 'top');
    }
});

function tw_plugin_activate() {
    tw_get_admin_slug(); //Ensure option exists
    flush_rewrite_rules();
}
register_activation_hook(PS_PLUGIN_FILE, 'tw_plugin_activate');

add_filter('query_vars', function ($vars) {
    $vars[] = 'tw_login';
    $vars[] = 'payment_success';
    return $vars;
});

/**
 * --------------------------------------------------
 * TEMPLATE REDIRECT HANDLER
 * --------------------------------------------------
 */
add_action('template_redirect', function () {

    // PAYMENT SUCCESS PAGE
    if (get_query_var('payment_success')) {
        $file = PS_PLUGIN_PATH . 'templates/stripe-payment-success.php';
        if (file_exists($file)) {
            status_header(200); nocache_headers(); require $file;
        }
    }

    //Login/Logout/Backend Handler
    $action = get_query_var('tw_login');
    if (!$action) return;
    if ($action === 'logout') {
        if (is_user_logged_in()) {
            wp_logout();            // logout immediately
            wp_clear_auth_cookie(); // extra safety
        }

        wp_safe_redirect(home_url());
        exit;
    }
    if ($action === 'signin' || $action === 'backend') {
        wp_safe_redirect(wp_login_url()); 
        exit;
    }    
}, 0);

// LOGIN REDIRECTION LOGIC...After login
add_filter('login_redirect', function ($redirect_to, $request, $user) {
    if (!isset($user->roles)) return home_url();
    if (in_array('student', (array) $user->roles,true)) {
        return twUrl('PS_UDashboard');
    }
    return admin_url();
}, 10, 3);

/* Prevent students accessing wp-admin */
add_action('admin_init', function () {
    if (!is_user_logged_in()) return;

    $user = wp_get_current_user();

    if (in_array('student', (array) $user->roles,true)) {
        if (defined('DOING_AJAX') && DOING_AJAX) return;
        wp_safe_redirect(twUrl('PS_UDashboard'));
        exit;
    }
});

// PROTECT PORTAL PAGE.............
add_action('template_redirect', function () {
    if (!is_page('portal')) return;

    if (!is_user_logged_in()) {
        wp_safe_redirect(home_url('/signin'));
        exit;
    }

    $user = wp_get_current_user();
    if (!in_array('student', (array) $user->roles,true)) {
        wp_safe_redirect(home_url());
        exit;
    }
}, 20);

// LOGIN PAGE UI (LIGHTWEIGHT)..........
add_action('login_enqueue_scripts', function () { ?>
    <style>
        body.login { background:#1a1a2e }
        #login h1 a {
            background-image:url('<?php echo plugin_dir_url(__FILE__); ?>assets/images/logo.png');
            background-size:contain;
            height:80px;
            width:100%;
        }
        .wp-core-ui .button-primary {
            background:#0033fe;
            border:none;
        }
        #backtoblog,.wp-login-lost-password { display:none }
    </style>
<?php });

add_filter('login_headerurl', fn() => home_url());
add_filter('login_headertitle', fn() => get_bloginfo('name'));

// SAFE REWRITE FLUSH..............
add_action('update_option_tw_cadmin_url', function ($old, $new) {
    if ($old !== $new) {
        flush_rewrite_rules();
    }
}, 10, 2);

//register_activation_hook(PS_PLUGIN_FILE, 'flush_rewrite_rules');
//register_deactivation_hook(PS_PLUGIN_FILE, 'flush_rewrite_rules');
