<?php
if (!defined('ABSPATH')) exit; // Security

//Admin Page Settings.........
add_action('admin_init', 'register_settings');
add_action('admin_menu', 'register_menus');


function register_settings() {
        add_settings_section( 'tw_email_section', '', '__return_false', 'techwatt-smtp' );

        ///////////////// Kleanaid Admin Email Settings /////////////////
        /*add_settings_field( 'tw_setting_hr1', '', function() {
            echo '<div style="width:100%; padding:15px 0 0 0;font-size:20px;font-weight:600;">Techwatt SMTP Settings<hr style="border:1px solid #999;margin:5px 0;"></div>';
        }, 'techwatt-smtp', 'tw_email_section' );*/

        add_settings_field( 'tw_from_email', 'Email Address:', function() {
            echo '<input type="email" name="tw_from_email" value="'.esc_attr(get_option('tw_from_email')).'" class="regular-text">'; }, 'techwatt-smtp', 'tw_email_section' );

        add_settings_field( 'tw_from_name', 'From Name:', function() {
            echo '<input type="text" name="tw_from_name" value="'.esc_attr(get_option('tw_from_name')).'" class="regular-text">'; }, 'techwatt-smtp', 'tw_email_section' );

        add_settings_field( 'tw_smtp_server', 'SMTP server:', function() {
            echo '<input type="text" name="tw_smtp_server" value="'.esc_attr(get_option('tw_smtp_server')).'" class="regular-text">'; }, 'techwatt-smtp', 'tw_email_section' );

        add_settings_field( 'tw_smtp_port', 'SMTP Port:', function() {
            echo '<input type="text" name="tw_smtp_port" value="'.esc_attr(get_option('tw_smtp_port')).'" class="regular-text">'; }, 'techwatt-smtp', 'tw_email_section' );

        add_settings_field( 'tw_smtp_secure', 'SMTP Secure:', function() {
            echo '<select name="tw_smtp_secure" class="regular-text"><option value="ssl"'.(esc_attr(get_option('tw_smtp_secure') === 'ssl' ? ' selected':'')).'>SSL</option><option value="tls"'.(esc_attr(get_option('tw_smtp_secure') === 'tls' ? ' selected':'')).'>TLS</option></select>'; }, 'techwatt-smtp', 'tw_email_section' );

        add_settings_field( 'tw_smtp_username', 'SMTP Username (email address):', function() {
            echo '<input type="text" name="tw_smtp_username" value="'.esc_attr(get_option('tw_smtp_username')).'" class="regular-text">'; }, 'techwatt-smtp', 'tw_email_section' );

        add_settings_field( 'tw_smtp_pwd', 'SMTP Password:', function() {
            echo '<input type="password" name="tw_smtp_pwd" value="'.esc_attr(get_option('tw_smtp_pwd')).'" class="regular-text">'; }, 'techwatt-smtp', 'tw_email_section' );
        
        add_settings_field( 'tw_stripe_pk', 'Stripe Public Key:', function() {
            echo '<input type="password" name="tw_stripe_pk" value="'.esc_attr(get_option('tw_stripe_pk')).'" class="regular-text">'; }, 'techwatt-smtp', 'tw_email_section' );
        
        add_settings_field( 'tw_stripe_sk', 'Stripe Secret Key:', function() {
            echo '<input type="password" name="tw_stripe_sk" value="'.esc_attr(get_option('tw_stripe_sk')).'" class="regular-text">'; }, 'techwatt-smtp', 'tw_email_section' );
        
         ////////////////////////////////////////////////
        register_setting( 'techwatt-smtp', 'tw_from_email');
        register_setting( 'techwatt-smtp', 'tw_from_name');
        register_setting( 'techwatt-smtp', 'tw_smtp_server');
        register_setting( 'techwatt-smtp', 'tw_smtp_port');
        register_setting( 'techwatt-smtp', 'tw_smtp_secure');
        register_setting( 'techwatt-smtp', 'tw_smtp_username');
        register_setting( 'techwatt-smtp', 'tw_smtp_pwd');
        
        register_setting( 'techwatt-smtp', 'tw_stripe_pk');
        register_setting( 'techwatt-smtp', 'tw_stripe_sk');
    }

    ////////////////////
    function register_settings_page() {
        if (!current_user_can('manage_options')) return;
        ?>
        <div class="wrap">
            <h1>Techwatt Stripe & SMTP Configuration</h1>
            <form method="post" action="options.php">
                <?php
                    settings_fields('techwatt-smtp');
                    do_settings_sections('techwatt-smtp');
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    ///////////////////////
    function register_menus() {
        add_submenu_page('techwatt-dashboard', 'SMTP Config', 'SMTP Config', 'manage_options', 'techwatt-smtp', 'register_settings_page');
    }
   