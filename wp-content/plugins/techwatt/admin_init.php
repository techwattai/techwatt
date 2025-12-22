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

    /*/ Submenu: Settings (points to your plugin settings page)
    add_submenu_page(
        'techwatt-dashboard',
        'Settings',
        'Settings',
        'manage_options',
        'techwatt-settings',
        'ps_render_settings_page' // 👈 Using the function we already built earlier
    );*/

    // Remove the automatic duplicate submenu
    global $submenu;
    if ( isset($submenu['techwatt-dashboard'][0][0]) && $submenu['techwatt-dashboard'][0][0] === 'Techwatt' ) {
        $submenu['techwatt-dashboard'][0][0] = 'Dashboard';
    }

});
