<?php
if (!isset($_GET['session_id'])) {
    wp_die("Session ID missing.");
}

if ( ! class_exists('\Stripe\Stripe') ) {
    require_once PS_PLUGIN_PATH . 'assets/vendor/autoload.php';
}
\Stripe\Stripe::setApiKey(twStripeKeys()['sk']);

global $wpdb;
$table = $wpdb->prefix . 'course_payments';
$session_id = sanitize_text_field($_GET['session_id']);

try {
    $session = \Stripe\Checkout\Session::retrieve($session_id);
    $uid     = $session->metadata->uid ?? null;
    $childid = $session->metadata->childid ?? null;
    $email   = $session->customer_email ?? '';

    $currency = strtoupper($session->currency);
    $amount_paid_cents = $session->amount_total;
    $amount = $amount_paid_cents / 100;

    $paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);
            if ($paymentIntent->status === 'succeeded') {
                $tw_userdata = get_user_meta($uid, 'tw_userdata', true) ?? [];

                if (isset($tw_userdata['children'][$childid])) {
                    $tw_userdata['children'][$childid]['paymentstatus'] = 'paid';
                    $PastPayment = floatval($tw_userdata['children'][$childid]['paid'] ?? 0);
                    $tw_userdata['children'][$childid]['paid'] = $PastPayment + $amount;
                    $tw_userdata['totalpaid'] = (floatval($tw_userdata['totalpaid'] ?? 0)) + $amount;

                    $tw_userdata['children'][$childid]['paid_currency'] = $currency;
                    $tw_userdata['children'][$childid]['paid_at'] = current_time('mysql');
                    $tw_userdata['children'][$childid]['paid_session'] = $session_id;
                    
                    $coursename = $tw_userdata['children'][$childid]['course'] ?? '';
                    $parentname = $tw_userdata['parentname'] ?? '';

                    update_user_meta($uid, 'tw_userdata', $tw_userdata);
                }

                update_user_meta(get_current_user_id(), "last_payment_session", $session_id);

                //update payment table log//////////
                $wpdb->update( $table, [ 'payment_status' => 'paid','parent_name'=>sanitize_text_field($parentname),'course'=>sanitize_text_field($coursename)], [ 'refno' => $session_id ], [ '%s','%s','%s' ], [ '%s' ] );
                //////// end of update /////////////////////////

                $msg = "Hello,\n\nA payment has been successfully made on techwatt.ai\n\n";
                $msg .= "Account ID: #".$uid."\n";
                $msg .= "Child Course ID: #".$childid."\n";
                $msg .= "Amount Paid: ".number_format($amount,2)." (".$currency.")\n";
                $msg .= "Payment Session ID: ".$session->id."\n";
                $msg .= "Payment Time: ".current_time('mysql')."\n\n";
                $msg .= "Regards,\nTechwatt  Team";
                
                $adminEmail = get_option('admin_email');

                if(!empty($email) && is_email($email)){
                    PS_SendMail($email,"Your Payment Confirmation on techwatt.ai",$msg);
                }
                if(!empty($adminEmail) && is_email($adminEmail)){
                    PS_SendMail($adminEmail,"Payment successfully made on techwatt.ai",$msg);
                } 
                wp_safe_redirect(twUrl('PS_UConfirm')."&status=paysuccess&uid=".$uid."&childid=".$childid );

            } else {
                $wpdb->update( $table, [ 'payment_status' => 'failed' ], [ 'refno' => $session_id ], [ '%s' ], [ '%s' ] );
                wp_safe_redirect(twUrl('PS_UConfirm')."&status=payfail&uid=".$uid."&childid=".$childid );
            }
    exit;

} catch (Exception $e) {
    //wp_die("Stripe Error: " . $e->getMessage());
}