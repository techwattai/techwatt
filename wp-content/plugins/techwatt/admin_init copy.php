<?php
//////// Admn Menu Pages ////////////////
add_action('admin_menu', function () {
    // Main Menu
    add_menu_page(
        'Techwatt Dashboard',        // Page Title
        'Techwatt',                  // Menu Title
        'manage_options',            // Capability
        'techwatt-dashboard',        // Slug
        'techwatt_dashboard_page',   // Callback
        'dashicons-welcome-learn-more', // Icon
        6                            // Position
    );

    // Add real Dashboard submenu
    add_submenu_page(
        'techwatt-dashboard',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'techwatt-dashboard',
        'techwatt_dashboard_page'
    );

    // Submenu: Register Students
    add_submenu_page(
        'techwatt-dashboard',  //Parent
        'Register Students', //Page Title
        'Students', //Menu Title
        'manage_options', // Capability
        'techwatt-register-students', //Slug
        'techwatt_register_students_page' //callback function
    );

// Submenu: Bookcamp registration
add_submenu_page('techwatt-dashboard','Bootcamp','Bootcamp','manage_options', 'techwatt-bootcamp', 'techwatt_bootcamp_page');

// Submenu: LMS
add_submenu_page('techwatt-dashboard','Future Innovators Club','Future Innovators Club','manage_options', 'techwatt-fclub', 'techwatt_fclub_page');

    // Submenu: Projects
    add_submenu_page(
        'techwatt-dashboard',
        'Projects',
        'Projects',
        'manage_options',
        'techwatt-projects',
        'techwatt_projects_page'
    );

    // Submenu: Testimonials
    add_submenu_page(
        'techwatt-dashboard',
        'Testimonials',
        'Testimonials',
        'manage_options',
        'techwatt-testimonials',
        'techwatt_testimonials_page'
    );

    //Submenu: Payments
    add_submenu_page(
        'techwatt-dashboard',
        'Course Revenue',
        'Course Revenue',
        'manage_options',
        'techwatt-payments',
        'techwatt_payments_page'
    );

    // Submenu: Settings (points to your plugin settings page)
    add_submenu_page(
        'techwatt-dashboard',
        'Settings',
        'Settings',
        'manage_options',
        'techwatt-settings',
        'ps_render_settings_page' // 👈 Using the function we already built earlier
    );

    // Remove the automatic duplicate submenu
    global $submenu;
    if ( isset($submenu['techwatt-dashboard'][0][0]) && $submenu['techwatt-dashboard'][0][0] === 'Techwatt' ) {
        $submenu['techwatt-dashboard'][0][0] = 'Dashboard';
    }

});

//////// Admin Settings Page /////////////

add_action('init', function () {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        if (in_array('student', (array) $user->roles)) {
            // Allow AJAX calls to still work
            if (defined('DOING_AJAX') && DOING_AJAX) { return; }
            if (strpos($_SERVER['REQUEST_URI'], '/wp-admin') !== false) {
                wp_redirect(twUrl("PS_UDashboard"));  exit;
            }
        }
    }
});
// Handle form submission
/*
if(isset($_POST["tw_cadmin_url"]) && !empty($_POST["tw_cadmin_url"])){
        $slug = sanitize_title($_POST["tw_cadmin_url"]) ?? get_option('tw_cadmin_url','');
        add_rewrite_rule("^{$slug}/?$", 'wp-login.php', 'top'); // register new rule
        flush_rewrite_rules(); // flush so it persists
}    

add_action('admin_init', function () {
    
    if (isset($_POST['ps_save_settings']) && check_admin_referer('ps_save_settings')) {
        $is_enabled = isset($_POST['ps_iscaurl']) ? true : false;
        $slug = sanitize_title($_POST['ps_caurl']);

        // Validate slug: only one "word", no slashes or spaces
        if ($is_enabled && !empty($slug)) {
            if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
                add_settings_error('ps_messages', 'ps_message', 'Invalid slug. Please use only letters, numbers, and hyphens (no spaces).', 'error');
                return; // Stop saving invalid slug
            }
        }

        $data = ['is_cadmin_url' => $is_enabled, 'cadmin_url' => $slug];
        ps_save_settings($data);
        add_settings_error('ps_messages', 'ps_message', 'Settings saved', 'updated');
    }
    
});*/

// Apply custom login URL
add_action('init', function () {
    $tw_cadmin_url = get_option('tw_cadmin_url');

    if (!empty($tw_cadmin_url)) {
        $slug = sanitize_title($tw_cadmin_url);

        // Rewrite rule for custom login
        add_rewrite_rule("^{$slug}/?$", 'wp-login.php', 'top');

        // Redirect wp-login.php requests
        if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false && $_SERVER['REQUEST_METHOD'] === 'GET') {
            wp_redirect(home_url("/{$slug}/"));
            exit;
        }

        // Change login_url site-wide so all links point to /backend
        add_filter('login_url', function ($url, $redirect, $force_reauth) use ($slug) {
            $args = [];
            if (!empty($redirect)) { $args['redirect_to'] = urlencode($redirect); }
            if ($force_reauth) { $args['reauth'] = '1'; }
            return add_query_arg($args, home_url("/{$slug}/"));
        }, 10, 3);

        // Fix logout URL
        add_filter('logout_url', function($logout_url, $redirect) use ($slug) {
            $args = ['action' => 'logout', '_wpnonce' => wp_create_nonce('log-out')];
            if (!empty($redirect)) { $args['redirect_to'] = $redirect; }
            else{ $args['redirect_to'] = home_url(); }
            return add_query_arg($args, home_url("/{$slug}/"));
        }, 10, 2);

        // Customize forgot password url..
        add_filter('lostpassword_url', function($url, $redirect) use ($slug) {
            $args = ['action' => 'lostpassword'];
            return add_query_arg($args, home_url("/{$slug}/")); // replace with your custom page
        }, 10, 2);
    }
});

//////////// Admin Login Page Styling ///////////////
/*
add_filter('logout_redirect', function($redirect_to, $requested_redirect_to, $user) {
    return home_url(); // redirect to homepage
}, 10, 3);
*/

// Change WordPress login logo and background color
add_action('login_enqueue_scripts', function() {
    ?>
    <style type="text/css">
        body.login {background-color: #1a1a2e;}

        /* Replace the WordPress logo */
        #login h1 a {
            background-image: url('<?php echo plugin_dir_url(__FILE__); ?>assets/images/logo.png');
            height: 80px; width: 100%;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Style login box */
        .login form {
            border-radius: 8px;
            padding: 30px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Change button color */
        .wp-core-ui .button-primary {
            background: #0033fe !important;
            border-color: #0033fe;
            text-shadow: none;
            box-shadow: none;
        }

        .wp-core-ui .button-primary:hover {
            background: #0033fe;
            border-color: #0033fe;
        }
    </style>
    <?php
});

// Optional: change logo link to your homepage
add_filter('login_headerurl', function() {  return home_url(); });

// Optional: change logo title on hover
add_filter('login_headertitle', function() { return get_bloginfo('name'); });

add_action('login_footer', function() {
    $lost_password_url = wp_lostpassword_url();
    $user_login_url = home_url(twUrl("PS_Login"));

    echo '<p id="nav" class="cflogurl">Do you <a href="'.esc_url($lost_password_url).'">forgot your password?</a></p>';
});


add_action('login_enqueue_scripts', function() {
    echo '<style>#backtoblog,.wp-login-lost-password { display:none !important; }.cflogurl{color:rgba(255,255,255,0.7);text-align:center;margin:0px auto;padding:0;}.cflogurl a{color:rgba(255,255,255,0.7) !important;text-decoration:underline !important;}</style>';
});
