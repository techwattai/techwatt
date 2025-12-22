<?php
if (!defined('ABSPATH')) exit;
// Register Sendgrid and create api key, Add this variable to Railway: SENDGRID_API_KEY=your_api_key_here

function tw_log($message) {
    if (is_array($message) || is_object($message)) {
        $message = wp_json_encode($message);
    }
    error_log('[TW-SENDGRID] ' . $message);
}

function tw_is_railway() {
    return getenv('RAILWAY_ENVIRONMENT') !== false;
}

// SENDGRID MAILER (NON-BLOCKING)
function tw_sendgrid_mail($to, $subject, $message) {
    //$api_key = $_ENV['SENDGRID_API_KEY'] ?? '';
    $api_key = getenv('SENDGRID_API_KEY') ?? '';

    if (!$api_key) {
        tw_log('SendGrid API key missing');
        return false;
    }

    $email_from = get_option('tw_smtp_username');
    $reply_to  = get_option('tw_from_email');
    $from_name  = get_option('tw_from_name');

    $body = [
        'personalizations' => [[
            'to' => [['email' => $to]],
        ]],
        'from' => [
            'email' => $email_from,
            'name'  => $from_name,
        ],
        'reply_to' => [
            'email' => $reply_to,
        ],
        'subject' => $subject,
        'content' => [[
            'type'  => 'text/html',
            'value' => $message,
        ]]
    ];

    $response = wp_remote_post('https://api.sendgrid.com/v3/mail/send', [
        'timeout' => 10,
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
            'List-Unsubscribe' => '<mailto:' . $reply_to . '>',
        ],
        'body' => wp_json_encode($body),
    ]);

    if (is_wp_error($response)) {
        tw_log('SendGrid error: ' . $response->get_error_message());
        return false;
    }

    //return true;
    $status = wp_remote_retrieve_response_code($response);
    $body   = wp_remote_retrieve_body($response);

    tw_log('SendGrid status/response: '.$status.' / '.$body);

    return ($status === 202);
}

// UNIFIED MAIL FUNCTION....................
function tw_send_mail($to, $subject, $message) {
    if (tw_is_railway()) {
        return tw_sendgrid_mail($to, $subject, $message);
    }
    return wp_mail($to, $subject, $message);
}

// SCHEDULE EMAIL (SAFE)............
function ScheduleEmail($to, $subject, $msg) {
    if (!$to || !$subject || !$msg) return;
    tw_send_mail($to, $subject, $msg);
    // $args = compact('to', 'subject', 'msg');
    // if (!wp_next_scheduled('tw_send_mail_event', $args)) { wp_schedule_single_event(time() + 5, 'tw_send_mail_event', $args); }
}

// CRON HANDLER........................
add_action('tw_send_mail_event', function ($args) {
    tw_send_mail($args['to'], $args['subject'], $args['msg']);
});

// SAFE PAGE-LOAD TEST (NO 504)...............
add_action('init', function () {
    //if (is_admin()) return;
    if (isset($_GET['testemail']) && $_GET['testemail'] == 1) {
        ScheduleEmail(
            'talk2gfavour@gmail.com',
            'Techwatt Scheduled Msg',
            "Hello,\nWelcome to Techwatt!"
        );
    }
});
