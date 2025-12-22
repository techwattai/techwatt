<?php
if (!defined('ABSPATH')) exit;

// CHECK IF MAIL SETTINGS ARE OK....
function tw_mail_settings_ok() {
    $required = [
        'tw_from_email',
        'tw_from_name',
        'tw_smtp_server',
        'tw_smtp_port',
        'tw_smtp_secure',
        'tw_smtp_username',
        'tw_smtp_pwd',
    ];

    foreach ($required as $option) {
        if (!get_option($option)) {
            return false;
        }
    }

    return true;
}

// PHPMailer SMTP CONFIGURATION.................
function ps_phpmailer_smtp_config($phpmailer) {
    if (!tw_mail_settings_ok()) {
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host       = get_option('tw_smtp_server');
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = get_option('tw_smtp_port');
    $phpmailer->SMTPSecure = get_option('tw_smtp_secure'); // ssl or tls
    $phpmailer->Username   = get_option('tw_smtp_username');
    $phpmailer->Password   = get_option('tw_smtp_pwd');
    $phpmailer->From       = get_option('tw_from_email');
    $phpmailer->FromName   = get_option('tw_from_name');

    // Uncomment for debugging
    // $phpmailer->SMTPDebug = 2;
}

add_action('phpmailer_init', 'ps_phpmailer_smtp_config');
// Prevent WP from overriding FROM details
add_filter('wp_mail_from', function () { return get_option('tw_from_email'); });
add_filter('wp_mail_from_name', function () { return get_option('tw_from_name'); });

// SCHEDULE EMAIL FUNCTION...........................
function ScheduleEmail($to = '', $subject = '', $msg = '') {

    if (!tw_mail_settings_ok()) { return false; }

    // Manual test email..................
    if (isset($_GET['send_test_email'])) {
        wp_mail(
            'favoursdot@gmail.com',
            'Techwatt message',
            "Dear Francis,\nThis is to notify you that your email is successfully sent!"
        );
        return true;
    }

    // Schedule email........
    if ($to && $subject && $msg) {
        $args = [
            'email'   => $to,
            'subject' => $subject,
            'msg'     => $msg,
        ];

        // Prevent duplicate scheduling..........
        if (!wp_next_scheduled('tw_send_welcome_mail', $args)) {
            wp_schedule_single_event(
                time() + 5,
                'tw_send_welcome_mail',
                $args
            );
        }
    }
}

// CRON HANDLER............................
add_action('tw_send_welcome_mail', function ($args) {
    wp_mail($args['email'], $args['subject'], $args['msg']);
});

// RUN ScheduleEmail ON PAGE LOAD.......................
add_action('init', function () {
    if (is_admin()) { return;  }

    if (isset($_GET['schedule_welcome']) && $_GET['schedule_welcome'] == 1) {
        ScheduleEmail(
            'techwattsai@gmail.com',
            'Welcome to Techwatt',
            "Hello,\nWelcome to Techwatt!"
        );
    }
});
