<?php
// Register shortcode for the form
function futureclub_registration_form() {
    ob_start(); 
    ?>
    
    <div id="msgfutureclub" style="padding:20px;border-radius:1px;min-height:350px;width:100%;text-align:center;display:none;"></div>

    <form id="futureclub-form" method="post" action="<?php echo admin_url("admin-ajax.php"); ?>" style="display:<?php echo($justValidated)?'none':'block'; ?>;">
        <h2>Join The Club</h2>        
        <input type="hidden" name="action" value="futureclub_submit">
        <?php wp_nonce_field( 'tw_futureclub', 'tw_futureclub_nonce' ); ?>
        <div style="display:flex;flex-direction:row;gap:10px;">
            <p class="ps-mb-5"><label>First Name *</label><input type="text" name="first_name"></p>
            <p class="ps-mb-5"><label>Last Name *</label><input type="text" name="last_name"></p>
        </div>
        <div style="display:flex;flex-direction:row;gap:10px;">
            <p class="ps-mb-5"><label>Email Address *</label><input type="email" name="email"></p>
            <p class="ps-mb-5"><label>Phone <small>(Whatsapp no preferred)</small> *</label><input type="text" name="phone" placeholder="+449180624802"></p>
        </div>
            <p class="ps-mb-5"><label>Residential Address</label><input type="text" name="address"></p>
            <p class="ps-mb-5"><label>Country *</label><input type="text" name="country"></p>
        <div style="display:flex;flex-direction:row;gap:10px;">
            <p class="ps-mb-5"><label>Company</label><input type="text" name="company"></p>
            <p class="ps-mb-5"><label>Profession *</label><input type="text" name="profession"></p>
        </div>
        <input type="submit" name="futureclub_submit" value="Submit" style="margin-top:10px;">
    </form>
    
    <script>
    jQuery(document).ready(function($){
        $(document).on("click", "#futureclub_cancel", function(){
            $("#msgfutureclub").hide();
            $("#futureclub-form").trigger("reset");
            $("#futureclub-form").show();
        });

        $('#futureclub-form').on('submit', function(e){
            e.preventDefault();
            let form = $(this);
            let url = form.attr('action');
            
            let btn = form.find('[type="submit"]');
            let originalText = btn.val();
            btn.val('Submitting...').prop('disabled', true);

            $.post(url, form.serialize(), function(response) {
                if (response.success) {
                    $('#msgfutureclub').html('<div><i class="bi bi-check-circle" style="font-size:40px;color:green;"></i><h2>Confirmation</h2><p style="font-size:18px;">'+response.data.message+'</p><a href="javascript:;" class="btn btn-primary" id="futureclub_cancel">Close</a></div>').show();
                    btn.val(originalText).prop('disabled', false);
                    $('#futureclub-form').hide();
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
add_shortcode('futureclub_registration', 'futureclub_registration_form');

///////////////////////
add_action('wp_ajax_futureclub_submit', 'fxn_futureclub_submit');
add_action('wp_ajax_nopriv_futureclub_submit', 'fxn_futureclub_submit');

function fxn_futureclub_submit() {
    $errors = new WP_Error();
    if (!check_ajax_referer('tw_futureclub', 'tw_futureclub_nonce', false) ) {
        wp_send_json_error(['status'=>'error','message' => 'Unauthorized request!']); return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'futureclub_registrations';
    $current_url = home_url($_SERVER['REQUEST_URI']);

    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name  = sanitize_text_field($_POST['last_name']);
    $email      = sanitize_email($_POST['email']);
    $phone      = sanitize_text_field($_POST['phone']);
    $isPhoneValid = isCCPhoneNo($phone);
    $address    = sanitize_text_field($_POST['address']);
    $company = sanitize_text_field($_POST['company']);
    $profession = sanitize_text_field($_POST['profession']);
    $memberid = strtoupper(GenerateUID('FC-',8));

    if(empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($profession)){
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
        'memberid'   => $memberid,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'email'      => $email,
        'phone'      => $phone,
        'address'    => $address,
        'country'    => $country,
        'company'    => $company,
        'profession' => $profession,
        'created_at' => current_time('mysql'),
    ]);

    PS_SendMail($email, 'Future Innovators Club Registration Received', "Dear $first_name $last_name,\n\nThank you for registering to join our Future Innovators Club. Your registration has been successfully received.\n\nMember ID: $memberid\n\nWe will contact you with further details information as soon as possible.\n\nBest regards,\nTechwatt Team");

    wp_send_json_success(['message' => 'Your Future Innovators Club application has been submitted successfully. Please check your email inbox for confirmation.']);
}
////////////////////// Create table on plugin activation
function futureclub_install() {
    global $wpdb;
    $table = $wpdb->prefix . 'futureclub_registrations';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        memberid varchar(40),
        first_name varchar(50),
        last_name varchar(50),
        email varchar(100),
        phone varchar(40),
        address varchar(150),
        country varchar(40),
        company varchar(40),
        profession varchar(40),
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(PS_PLUGIN_FILE, 'futureclub_install');
