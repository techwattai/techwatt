<?php
// Register a pretty permalink endpoint
function ps_logout_rewrite() {
    add_rewrite_rule( '^logout/?$', 'index.php?logout=1', 'top' );
    add_rewrite_rule( '^signin/?$', 'index.php?signin=1', 'top' );
}
add_action( 'init', 'ps_logout_rewrite' );

// Add custom query var
function ps_logout_query_vars( $vars ) {
    $vars[] = 'logout';
    return $vars;
}
add_filter( 'query_vars', 'ps_logout_query_vars' );

function ps_login_query_vars( $vars ) {
    $vars[] = 'signin';
    return $vars;
}
add_filter( 'query_vars', 'ps_login_query_vars' );

// Handle login request
function ps_login_template_redirect() {
    if ( get_query_var('signin') ) { //just load wp-login.php
        require_once ABSPATH.'wp-login.php';
        exit;
    }
}
add_action( 'template_redirect', 'ps_login_template_redirect' );

// Handle logout
function ps_logout_template_redirect() {
    if ( get_query_var( 'logout' ) ) {
        wp_logout();
        wp_safe_redirect( home_url() ); // or /goodbye/
        exit;
    }
}
add_action( 'template_redirect', 'ps_logout_template_redirect' );

add_action( 'template_redirect', function() {
    if ( is_404() ) {
        status_header( 404 );   // Send proper HTTP 404 header
        nocache_headers();      // Prevent caching
?>
<!DOCTYPE html>
        <html>
        <head>
            <title>Techwatt - 404 Page Not Found</title>
            <style>
                body { font-family: Arial, sans-serif; text-align:center; padding:50px; background: #092286ff;}
                h1 { font-size:40px; color:#111; margin:5px 0px; }
                p { font-size:18px; }
                a { color:#0073aa; text-decoration:none;padding:0 5px;}
                .errContainer { background: #fff; padding: 30px; border-radius: 8px; display: inline-block; box-shadow: 0 0 10px rgba(0,0,0,0.1);width:70%;margin:5% auto;}
            </style>
        </head>
        <body>
            <div class="errContainer">
                <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/logo.png'; ?>" alt="Techwatt Logo" style="max-width:100px;height:auto;margin-top:-70px;">
                <h1>Page Not Found</h1>
                <p>The requested URL <a href="<?php echo PS_GetCurrentUrl(); ?>"><?php echo PS_GetCurrentUrl(); ?></a> was not found on this server. Try links below.</p>
                <p><a href="<?php echo esc_url( home_url() ); ?>">Home</a> | <a href="<?php echo esc_url( home_url('/shop') ); ?>">Products</a> | <a href="<?php echo esc_url( home_url('/courses') ); ?>">Courses</a> | <a href="<?php echo esc_url( home_url('/contact') ); ?>">Contacts</a></p>
            </div>
        </body>
        </html>
<?php
    exit; // prevent WordPress from loading theme’s 404.php
    }
});
?>