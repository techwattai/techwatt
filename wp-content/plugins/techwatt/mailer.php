<?php
if (!defined('ABSPATH')) exit; // Security

function ps_phpmailer_smtp_config($phpmailer){
    $from_email = get_option('tw_from_email');
    $from_name = get_option('tw_from_name');
    $smtp_server = get_option('tw_smtp_server');
    $smtp_port = get_option('tw_smtp_port');
    $smtp_secure = get_option('tw_smtp_secure');
    $smtp_username = get_option('tw_smtp_username');
    $smtp_pwd = get_option('tw_smtp_pwd');

    // === SMTP SETTINGS === //
    $phpmailer->isSMTP();
    $phpmailer->Host       = $smtp_server;     // your SMTP server
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = $smtp_port; // 465 SSL or 587 TLS
    $phpmailer->SMTPSecure = $smtp_secure;  // 'ssl' or 'tls'

    $phpmailer->Username   = $smtp_username;    // SMTP username
    $phpmailer->Password   = $smtp_pwd; // SMTP password

    // === FROM DETAILS === //
    $phpmailer->From       = $from_email;
    $phpmailer->FromName   = $from_name;
    // Optional debug output to logs
    // $phpmailer->SMTPDebug = 2;
}

 //////////////
    function ScheduleEmail($to='',$subject='',$msg=''){ //https://techwatt.ai/?send_test_email=1
        $header = array('Content-Type: text/plain; charset=UTF-8');

        if(isset($_GET['send_test_email'])) {
            wp_mail('favoursdot@gmail.com', 'Techwatt message', "Dear Francis,\nThis is to notify you that your email is successfully sent!",$header);
            echo "Test email triggered!";
        }else{
            if($to != '' && $subject != '' && $msg != ''){
                wp_schedule_single_event(time() + 5, 'tw_send_welcome_mail', ['email'=>$to,'subject'=>$subject,'msg'=>$msg]);
            }     
        }
}
////////////////////


//////////////////////////////////////
// Configure PHPMailer
add_action('phpmailer_init', 'ps_phpmailer_smtp_config');
// Disable WordPress overriding "from" fields
add_filter('wp_mail_from', function() { $from_email = get_option('tw_from_email'); return $from_email; });
add_filter('wp_mail_from_name', function() { $from_name = get_option('tw_from_name'); return $from_name; });

add_action('tw_send_welcome_mail', function($args){ 
    wp_mail($args['email'], $args['subject'], $args['msg']);
});
