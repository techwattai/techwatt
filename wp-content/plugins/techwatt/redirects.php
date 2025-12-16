<?php
// all code in logging-codes.php has been merged and with some code in general_init.php
// Unified Redirects & Routing for Techwatt

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add custom rewrite rules for friendly URLs
 */
function tw_add_rewrite_rules() {
    add_rewrite_rule( '^logout/?$', 'index.php?logout=1', 'top' );
    add_rewrite_rule( '^signin/?$', 'index.php?signin=1', 'top' );
}
add_action( 'init', 'tw_add_rewrite_rules', 1 );

/**
 * Add custom query vars
 */
function tw_add_query_vars( $vars ) {
    $vars[] = 'logout';
    $vars[] = 'signin';
    return $vars;
}
add_filter( 'query_vars', 'tw_add_query_vars' );

/**
 * Properly flush rewrite rules on plugin activation/deactivation.
 * Uses PS_PLUGIN_FILE constant defined in your main plugin file.
 */
if ( defined( 'PS_PLUGIN_FILE' ) ) {
    register_activation_hook( PS_PLUGIN_FILE, function() {
        // Ensure rules are registered before flush
        tw_add_rewrite_rules();
        flush_rewrite_rules( true );
    } );

    register_deactivation_hook( PS_PLUGIN_FILE, function() {
        flush_rewrite_rules( true );
    } );
}

/**
 * Very early: handle simple route-based actions (signin/logout).
 * Priority 1 to ensure they run BEFORE other template_redirect logic.
 */
function tw_handle_route_signin_logout() {
    // signin -> redirect to standard WP login page (optionally with redirect_to)
    if ( get_query_var( 'signin' ) ) {
        // Redirect to wp-login.php with a redirect_to back to home (or current URL)
        $redirect_to = home_url(); // change if you want a different post-login redirect
        wp_safe_redirect( wp_login_url( $redirect_to ) );
        exit;
    }

    // logout -> use wp_logout() then redirect to home
    if ( get_query_var( 'logout' ) ) {
        // Log the user out and redirect safely
        wp_logout();
        wp_safe_redirect( home_url() );
        exit;
    }
}
add_action( 'template_redirect', 'tw_handle_route_signin_logout', 1 );


/**
 * Portal protection and user restrictions.
 * Run after signin/logout (priority 20).
 */
function tw_protect_portal_pages() {
    // If you use a page slug 'portal' or a page ID, keep this check.
    if ( is_page( 'portal' ) || is_front_page() && ( false ) ) { // example, keep only portal()
        // Require login
        if ( ! is_user_logged_in() ) {
            // Redirect to your login route or WP login and preserve return URL
            $return_to = ( is_singular() ? get_permalink() : home_url( $_SERVER['REQUEST_URI'] ?? '/' ) );
            wp_safe_redirect( wp_login_url( $return_to ) );
            exit;
        }

        // Restrict to 'student' role only (adjust as needed)
        $user = wp_get_current_user();
        if ( ! in_array( 'student', (array) $user->roles ) ) {
            wp_safe_redirect( home_url() );
            exit;
        }
    }

    // If you have other portal internal routes that need protection, add them here:
    // e.g. if ( strpos( $_SERVER['REQUEST_URI'], '/portal' ) === 0 ) { ... }
}
add_action( 'template_redirect', 'tw_protect_portal_pages', 20 );


/**
 * Track product views (unchanged) - keep it separate and with mid priority
 */
function tw_track_product_views() {
    if ( is_singular( 'product' ) ) {
        global $post;
        if ( empty( $post ) ) return;
        $views = (int) get_post_meta( $post->ID, 'views_count', true );
        $views++;
        update_post_meta( $post->ID, 'views_count', $views );
    }
}
add_action( 'template_redirect', 'tw_track_product_views', 15 );


/**
 * Custom 404 template handling (run last so other redirects get precedence)
 *
 * NOTE: We include a separate template file inside the plugin for maintainability.
 * Place the file at: plugin-dir/templates/custom-404.php
 */
function tw_custom_404_handler() {
    if ( is_404() ) {
        status_header( 404 );
        nocache_headers();

        $tpl = plugin_dir_path( __FILE__ ) . 'templates/custom-404.php';
        if ( file_exists( $tpl ) ) {
            include $tpl;
        } else {
            // Fallback small 404 output if template missing
            echo '<!doctype html><html><head><meta charset="utf-8"><title>404 - Not Found</title></head><body><h1>404 - Not Found</h1></body></html>';
        }

        exit;
    }
}
add_action( 'template_redirect', 'tw_custom_404_handler', 999 );


/**
 * Login-time redirect block for student users (login_init)
 * If a student is already logged in and hits the login page, redirect away to dashboard.
 */
function tw_login_init_redirect() {
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();
        if ( in_array( 'student', (array) $user->roles ) ) {
            wp_safe_redirect( twUrl("PS_UDashboard") );
            exit;
        }
    }
}
add_action( 'login_init', 'tw_login_init_redirect' );

/**
 * login_redirect filter: after logging in, redirect students to dashboard
 */
function tw_login_redirect( $redirect_to, $request, $user ) {
    if ( isset( $user->roles ) && in_array( 'student', $user->roles ) ) {
        return twUrl("PS_UDashboard");
    }
    // default: let WP handle other roles
    return $redirect_to;
}
add_filter( 'login_redirect', 'tw_login_redirect', 10, 3 );
