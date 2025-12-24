<?php
// Enqueue scripts
/*
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('jquery');
    wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/');
});
*/

// Register shortcode for the form
function bootcamp_registration_form() {
    $bc = sanitize_text_field($_GET['bc'] ?? '');
    $orderID = sanitize_text_field($_GET['order_id'] ?? '');
    $sessionID = sanitize_text_field($_GET['session_id'] ?? '');
    $justValidated = false; $justHead = ''; $justMsg = '';

    if (isset($_GET['session_id'])) {        
        try {
            if ( ! class_exists('\Stripe\Stripe') ) {
                    require_once PS_PLUGIN_PATH . 'assets/vendor/autoload.php';
            }
            \Stripe\Stripe::setApiKey(twStripeKeys()['sk']);
            $session = \Stripe\Checkout\Session::retrieve($sessionID);

            if ($session->payment_status === 'paid') {
                $newStatus = 'success';
                $justHead = 'Congratulation!';
                $justMsg = 'Bootcamp successfully submitted, and payment was successful. Check your email for confirmation.';
            }else{
                $newStatus = 'failed';
                $justHead = 'Payment Failed';
                $justMsg = 'Your bootcamp registration has been submitted successfully, but the payment was not completed. You can make the payment later when the program begins.';
            }
        } catch (Exception $e) {
            $newStatus = 'failed';
            $justHead = 'Payment Verification Failed';
            $justMsg = 'Bootcamp successfully submitted but unable to verify your payment. Please contact support.';
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'bootcamp_registrations';
        $wpdb->update($table, ['payment_status' => $newStatus, 'updated_at' => current_time('mysql'),], ['order_id' => $orderID]);
        $justValidated = true;
    }

    ob_start(); 
    ?>
    
    <div id="msgbootcamp" style="padding:20px;border-radius:1px;min-height:350px;width:100%;text-align:center;display:<?php echo($justValidated)?'block':'none'; ?>;"><div><i class="bi bi-check-circle" style="font-size:40px;color:green;"></i><h2><?php echo esc_html($justHead); ?></h2><p style="font-size:18px;"><?php echo esc_html($justMsg); ?></p><a href="javascript:;" class="btn btn-primary" id="bootcamp_cancel">Cancel</a></div></div>

    <form id="bootcamp-form" method="post" action="<?php echo admin_url("admin-ajax.php"); ?>" style="display:<?php echo($justValidated)?'none':'block'; ?>;">
        <h3>Bootcamp</h3>        
        <input type="hidden" name="action" value="bootcamp_submit">
        <?php wp_nonce_field( 'tw_bootcamp', 'tw_bootcamp_nonce' ); ?>
        <p class="ps-mb-5"><label>First Name *</label><input type="text" name="first_name"></p>
        <p class="ps-mb-5"><label>Last Name *</label><input type="text" name="last_name"></p>
        <p class="ps-mb-5"><label>Email Address *</label><input type="email" name="email"></p>
        <p class="ps-mb-5"><label>Phone <small>(Whatsapp no preferred, include your country code, no space)</small> *</label><input type="text" name="phone" placeholder="+449180624802"></p>
        <p class="ps-mb-5"><label>Residential Address</label><input type="text" name="address"></p>
        <p class="ps-mb-5"><label>Bootcamp Type *</label>
        <select name="bootcamp_type" required><option value="">**Select**</option>
            <option value="free"<?php echo (strtolower($bc) == 'free')?' selected':''; ?>>Free Bootcamp</option>
            <option value="summer"<?php echo (strtolower($bc) == 'summer')?' selected':''; ?>>Summer Bootcamp</option>
            <option value="winter"<?php echo (strtolower($bc) == 'winter')?' selected':''; ?>>Winter Bootcamp</option>
            <option value="weekend"<?php echo (strtolower($bc) == 'weekend')?' selected':''; ?>>Weekend Bootcamp</option>
        </select>
        <!--<label>Price (USD)</label>-->
        <input type="hidden" name="price" step="0.01">		
		<input type="hidden" name="payment_option" value="paylater">
        <!--<label>Payment Option</label>
        <select name="payment_option" required>
            <option value="paylater">Pay Later</option>
            <option value="paynow">Pay Now</option>
        </select>-->
        <input type="submit" name="bootcamp_submit" value="Submit" style="margin-top:10px;">
    </form>
    
    <script>
    jQuery(document).ready(function($){
        $(document).on("click", "#bootcamp_cancel", function(){
            $("#msgbootcamp").hide();
            $("#bootcamp-form").trigger("reset");
            $("#bootcamp-form").show();
        });

        $('#bootcamp-form').on('submit', function(e){
            e.preventDefault();
            let form = $(this);
            let paymentOption = form.find('[name="payment_option"]').val();
            let url = form.attr('action');

            let btn = form.find('[type="submit"]');
            let originalText = btn.val();
            btn.val('Submitting...').prop('disabled', true);

            $.post(url, form.serialize(), function(response) {
                if (response.success) {
                    $('#msgbootcamp').html('<div><i class="bi bi-check-circle" style="font-size:40px;color:green;"></i><h2>Confirmation</h2><p style="font-size:18px;">'+response.data.message+'</p><a href="javascript:;" class="btn btn-primary" id="bootcamp_cancel">Cancel</a></div>').show();
                    btn.val(originalText).prop('disabled', false);
                    $('#bootcamp-form').hide();
                    if (response.url) window.location.href = response.data.url;
                } else {
                    btn.val(originalText).prop('disabled', false);
                    alert(response.data.message || 'An error occurred.');
                }
            }).fail(function(){
                btn.val(originalText).prop('disabled', false);
                alert('An error occurred. Try again or contact the administrator!');                
            });
        });
    });
</script>
<?php
    return ob_get_clean();
}
add_shortcode('bootcamp_registration', 'bootcamp_registration_form');

///////////////////////
add_action('wp_ajax_bootcamp_submit', 'fxn_bootcamp_submit');
add_action('wp_ajax_nopriv_bootcamp_submit', 'fxn_bootcamp_submit');

function fxn_bootcamp_submit() {
    $errors = new WP_Error();
    if (!check_ajax_referer('tw_bootcamp', 'tw_bootcamp_nonce', false) ) {
        wp_send_json_error(['status'=>'error','message' => 'Unauthorized request!']); return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'bootcamp_registrations';
    $current_url = home_url($_SERVER['REQUEST_URI']);

    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name  = sanitize_text_field($_POST['last_name']);
    $email      = sanitize_email($_POST['email']);
    $phone      = sanitize_text_field($_POST['phone']);
    $isPhoneValid = isCCPhoneNo($phone);
    $address    = sanitize_text_field($_POST['address']);
    $bootcamp_type = sanitize_text_field($_POST['bootcamp_type']);
    $price      = floatval($_POST['price']);
    $paymentOption = sanitize_text_field($_POST['payment_option']);
    $payment_status = 'pending';
    $order_id = strtoupper(GenerateUID('BC-',8));

    if(empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($bootcamp_type)){
        wp_send_json_error(['message' => 'Please fill in all required fields.']); return;
    }
    if(!is_email($email)){
        wp_send_json_error(['message' => 'Invalid email address.']); return;
    }
    if(!$isPhoneValid["status"]){
        wp_send_json_error(['message' => $isPhoneValid["message"]]); return;
    }
    // Insert registration data
    $insertID = $wpdb->insert($table, [
        'order_id'   => $order_id,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'email'      => $email,
        'phone'      => $phone,
        'address'    => $address,
        'bootcamp_type' => $bootcamp_type,
        'price'      => $price,
        'payment_status' => $payment_status,
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql'),
    ]);

    if ($paymentOption === 'paylater') {
        $if2payMsg = ''; $if2payMsg2 = '';
        if($bootcamp_type !== 'free'){
            $if2payMsg = ' regarding payment and next steps';
            $if2payMsg2 = ' You can make the payment later when the program begins.';
        }

        ScheduleEmail($email, 'Bootcamp Registration Received', "Dear $first_name $last_name,<p>Thank you for registering for the $bootcamp_type bootcamp. Your registration has been successfully received.</p><p>Order ID: $order_id</p><p>We will contact you with further details $if2payMsg.</p><p>Best regards,<br>Techwatt Team</p>");

        wp_send_json_success(['message' => 'Your bootcamp application has been submitted successfully.'.$if2payMsg2.' A confirmation email has been sent to your inbox.', 'url' => '']);return;
    }

    if($price <= 0){
        wp_send_json_error(['message' => 'Invalid price for bootcamp.']); return;
    }
    // Create Stripe Checkout Session
    \Stripe\Stripe::setApiKey(PS_STRIPE_SK);
    $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => ['currency' => 'gbp',
                    'product_data' => ['name' => ucfirst($bootcamp_type).' Bootcamp',],
                    'unit_amount' => $price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => add_query_arg(array('status' => 'success', 'order_id' => $order_id,'session_id' => '{CHECKOUT_SESSION_ID}',), $current_url),
            'cancel_url' => add_query_arg(array('status' => 'cancelled', 'order_id' => $order_id,), $current_url),
            'metadata' => [
                'order_id' => $order_id,
                'email' => $email,
            ]
        ]);
    //wp_redirect($session->url); exit;
    ScheduleEmail($email, 'Bootcamp Registration Received', "Dear $first_name $last_name,<p>Thank you for registering for the $bootcamp_type bootcamp. Your registration has been received.</p><p>Order ID: $order_id</p><p>We will contact you with further details regarding payment and next steps.</p><p>Best regards,<br>Techwatt Team</p>");

    wp_send_json_success(['message' => 'Bootcamp successfully submitted. Pay processed. Check your email for confirmation.', 'url' => $session->url]);
}
////////////////////// Create table on plugin activation
function bootcamp_install() {
    global $wpdb;
    $table = $wpdb->prefix . 'bootcamp_registrations';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        order_id varchar(40),
        first_name varchar(50),
        last_name varchar(50),
        email varchar(100),
        phone varchar(20),
        address text,
        bootcamp_type varchar(20),
        price float,
        payment_status varchar(20),
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(PS_PLUGIN_FILE, 'bootcamp_install');
