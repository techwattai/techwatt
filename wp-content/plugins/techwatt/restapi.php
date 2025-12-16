<?php
///////////////// REST API SECURITY /////////////////////////
add_action('rest_api_init', function () {
    register_rest_route('techwatt/v1', '/register', [ 'methods' => 'POST', 'callback'  => 'tw_rest_register', 'permission_callback' => '__return_true', ]);
});

add_action('rest_api_init', function () {
    register_rest_route('techwatt/v1', '/booktrial', [ 'methods' => 'POST', 'callback'  => 'tw_rest_booktrial', 'permission_callback' => '__return_true', ]);
});

add_action('rest_api_init', function () {
    register_rest_route('techwatt/v1', '/cstripe-pay', [ 'methods' => 'POST', 'callback'  => 'tw_rest_cstripepay', 'permission_callback' => '__return_true', ]);
});

add_action('rest_api_init', function () {
    register_rest_route('techwatt/v1', '/cancel-course', [ 'methods' => 'POST', 'callback'  => 'tw_rest_cancelcourse', 'permission_callback' => '__return_true', ]);
});
/////////////////////////////////////////////////


function tw_rest_cancelcourse(WP_REST_Request $req){
    $errors = new WP_Error();  $msg = '';
    
    $nonce = $req->get_header('X-WP-Nonce');
    if (!wp_verify_nonce($nonce, 'wp_rest')) { 
        return new WP_REST_Response([ 'success' => false, 'message' => 'Security verification failed' ], 200);
    }

    $uid       = sanitize_text_field($req->get_param('uid'));
    $childid   = sanitize_text_field($req->get_param('childid'));
    
    if (empty($uid) || empty($childid)) {
        return new WP_REST_Response([ 'success' => false, 'message' => 'Missing required parameters' ], 200);
    }
    $tw_userdata = get_user_meta($uid, 'tw_userdata', true) ?? [];
    
    if( empty($tw_userdata) || !isset($tw_userdata['children'][$childid]) ){
        return new WP_REST_Response([ 'success' => false, 'message' => 'Course not found!' ], 200);
    }

    //delete the child course
    unset($tw_userdata['children'][$childid]);
    update_user_meta($uid, 'tw_userdata', $tw_userdata);
    return new WP_REST_Response([ 'success' => true, 'message' => 'Course has been successfully cancelled.' ], 200);
}

function tw_rest_cstripepay(WP_REST_Request $req){
    global $wpdb;
    $table = $wpdb->prefix . 'course_payments';

    $errors = new WP_Error();  $msg = '';
    
    $nonce = $req->get_header('X-WP-Nonce');
    if (!wp_verify_nonce($nonce, 'wp_rest')) { //$_SERVER['HTTP_X_WP_NONCE']
        return new WP_REST_Response([ 'success' => false, 'message' => 'Security verification failed' ], 200);
    } 

    // Sanitize inputs
    $uid       = sanitize_text_field($req->get_param('uid'));
    $childid   = sanitize_text_field($req->get_param('childid'));
    $amount    = $req->get_param('amount');
    $currency  = strtolower(sanitize_text_field($req->get_param('currency')));
    $name      = sanitize_text_field($req->get_param('name'));
    $email     = sanitize_email($req->get_param('email')) ?: 'info@techwatt.ai';
    $product   = 'Techwatt Course Payment';

    if (!$amount || !$currency || empty($uid) || empty($childid)) {
        return new WP_REST_Response([ 'success' => false, 'message' => 'Missing required parameters' ], 200);
    }

    $amount_cents = intval(round($amount * 100));
    if ($amount_cents < 100) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Invalid amount'
        ], 200);
    }

    //return new WP_REST_Response([ 'success' => false, 'message' => 'Error 4564..'.PS_PLUGIN_PATH ], 200);
    if ( ! class_exists('\Stripe\Stripe') ) {
            $autoload = PS_PLUGIN_PATH . 'assets/vendor/autoload.php';
            if ( file_exists($autoload) ) {
                require_once $autoload;
            } else {
                return new WP_REST_Response(['success' => false, 'message' => 'Stripe library missing. Contact support.'], 200);
            }
    }
    
    \Stripe\Stripe::setApiKey(twStripeKeys()['sk']);

    try {
        // Create Stripe Checkout Session
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'customer_email' => $email,
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount' => $amount_cents,
                    'product_data' => [ 'name' => $product, ],
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'uid'     => $uid,
                'childid' => $childid,
                'email' => $email,
            ],
            'success_url' => home_url('/payment-success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url'  => home_url('/payment-cancelled'),
        ]);

        ///////////////// Save to payment table log; ///////////////////////
        $result = $wpdb->insert( $table, [
                'userid'        => $uid,
                'childid'       => $childid,
                'parent_name'    => $name,
                'course'         => '',
                'amount'         => $amount,
                'currency'       => strtoupper($currency),
                'payment_status' => 'pending',
                'refno' => $session->id,
                'created_at'     => current_time('mysql') ], [ '%d','%s','%s','%s','%f', '%s','%s','%s','%s'] );
        //$payment_id = $wpdb->insert_id;

        return new WP_REST_Response([ 'success' => true, 'message' => 'Payment processed successfully.', 'data' => [ 'id' => $session->id ] ], 200);
        
    } catch (Exception $e) {
        return new WP_REST_Response(['success' => false, 'message' => 'Payment processing error: ' . $e->getMessage()], 200);
    }
}



/////////////////////////////////////////
function tw_rest_booktrial(WP_REST_Request $request){
    $errors = new WP_Error();

    // Verify nonce
    
    $nonce = $request->get_header('X-WP-Nonce');
    if (!wp_verify_nonce($nonce, 'wp_rest')) { 
        return new WP_REST_Response([ 'success' => false, 'message' => 'Unauthorized request!' ], 200);
    }
    /*
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if (strpos($referer, home_url()) !== 0) {
        return new WP_REST_Response(['success' => false, 'message' => 'Unauthorized request!'], 200);
    }*/

    global $ArrayPackages;
    $refid = sanitize_text_field($_SESSION['twrefid'] ?? '');
    
    $uid          = absint($request->get_param('tw_uid'));
    $email        = sanitize_email($request->get_param('tw_email'));
    $password     = $request->get_param('tw_password');
    $cpassword    = $request->get_param('tw_cpassword');
    $childname    = sanitize_text_field($request->get_param('tw_childname'));
    $parentname   = sanitize_text_field($request->get_param('tw_parentname'));
    $country_code = sanitize_text_field($request->get_param('tw_country'));
    $phone        = sanitize_text_field($request->get_param('tw_phone'));
    $phone        = TrimPhoneNo($phone);
    $username     = sanitize_user($phone, true);
    $age          = sanitize_text_field($request->get_param('tw_age'));
    $course       = sanitize_text_field($request->get_param('tw_course'));
    $package      = sanitize_text_field($request->get_param('tw_package'));

    $cost     = isset($ArrayPackages[$package]['cost']) ? floatval($ArrayPackages[$package]['cost']) : 0;
    $duration = isset($ArrayPackages[$package]['duration']) ? sanitize_text_field($ArrayPackages[$package]['duration']) : '';
    
    // ✅ Validation
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors->add("invalid_phone", "Phone number must be 10 digits.");
    } elseif ($uid <= 0 && (empty($username) || !validate_username($username))) {
        $errors->add("invalid_username", "Invalid username. Avoid spaces or special characters in your phone number.");
    } elseif ($uid <= 0 && username_exists($username)) {
        $errors->add("username_exit", "Phone number already taken. Try another phone number!");
    } elseif ($uid <= 0 && !is_email($email)) {
        $errors->add("invalid_email", "Invalid email address.");
    } elseif ($uid <= 0 && email_exists($email)) {
        $errors->add("email_exist", "Email already exists. Try another email address!");
    } elseif ((empty($password) || strlen($password) < 8) && $uid <= 0) {
        $errors->add("wrong_password", "Password must be at least 8 characters.");
    } elseif (($password !== $cpassword) && $uid <= 0) {
        $errors->add("pwd_notmatch", "Passwords do not match!");
    } elseif (empty($course)) {
        $errors->add("empty_course", "Choose a course to enrol!");
    } elseif (empty($package)) {
        $errors->add("empty_package", "Choose a package to enrol!");
    }

    if ($errors->has_errors()) {
        $msg = '';
        foreach ($errors->get_error_messages() as $error) {
            $msg .= '<p>' . esc_html($error) . '</p>';
        }
        return new WP_REST_Response(['success' => false, 'message' => $msg], 200);
    }

    // Create user
    if( $uid > 0 && get_userdata($uid) ){
        $user_id = $uid;
        $pstatus = 'Pending';
        $successMsg = 'New course has been successfully created!';
        $paidNow = true;
    }else{
        $user_id = wp_create_user($username, $password, $email);
        $pstatus = 'Trial';
        $successMsg = 'Trial course successfully booked! Click <a href="'.esc_url(twUrl("PS_Login")).'">login</a> to access the course.';
        $paidNow = false;
        if (is_wp_error($user_id)) {
            return new WP_REST_Response(['success' => false, 'message' => $user_id->get_error_message()], 200);
        }
        $user = new WP_User($user_id);
        $user->set_role('student');

    }
    
    $regdate = time();
    $child_id = strtolower('tw-' . wp_generate_password(6, false, false));
    $childArray = [
            'id' => $child_id,
            'name' => $childname,
            'age' => $age,
            'course' => $course,
            'class' => '',
            'regdate' => $regdate,
            'package' => $package,
            'cost' => $cost,
            'paid' => '0',
            'duration' => $duration,
            'paymentstatus' => $pstatus
    ];

    $newchildren = [ $child_id => $childArray ];

    $flname = explode(" ", $parentname);
    $lname = $flname[0] ?? '';
    $fname = $flname[1] ?? '';
    $dname = !empty($fname) ? $fname : $parentname;

    if($uid <= 0){
        wp_update_user([
            'ID' => $user_id,
            'display_name' => $dname,
            'nickname' => $parentname,
            'first_name' => $fname,
            'last_name' => $lname
        ]);
    
        update_user_meta($user_id, 'tw_userdata', [
            'children' => $newchildren,
            'parentname' => $parentname,
            'countrycode' => $country_code,
            'phone' => $phone,
            'totalpaid' => 0.00,
            'regdate' => $regdate,
            'refid' => $refid
        ]);
    }else{
        $tw_userdata = get_user_meta($user_id, 'tw_userdata', true) ?? [];
        //$updated_children = array_merge($tw_userdata['children'] ?? [], $newchildren);
        $tw_userdata['children'][$child_id] = $childArray; // = $updated_children;
        $tw_userdata['refid'] = ($tw_userdata['refid'] != '') ? $tw_userdata['refid'] : $refid;
        update_user_meta($user_id, 'tw_userdata', $tw_userdata);
    }

    // Send emails
    $admin_email = get_option("tw_from_email");
    $subject = 'Techwatt Trial Class Booked';
    $msg = "Dear $parentname,\n\nYour techwatt trial class has been successfully created. Login with:\nUsername: $email or $username\nPassword: $password\n\nBest regards,\nTechwatt Team";
    ScheduleEmail($email, $subject, $msg);

    if (!empty($admin_email)) {
        ScheduleEmail($admin_email, "New trial class booked", "Dear admin,\n$parentname just booked a trial class on techwatt.ai. Check your admin backend.");
    }

    return new WP_REST_Response([
        'success' => true,
        'message' => $successMsg,
        'data' => [ 'uid' => $user_id, 'childid' => $child_id,"cost"=>$cost,"course"=>$course,"package"=>$package ],
        'paidnow' => $paidNow
    ], 200);
}

///////////////

function tw_rest_register(WP_REST_Request $req) {
    $errors = new WP_Error();  $msg = '';

    // Verify nonce
    $nonce = $req->get_header('X-WP-Nonce');
    if (!wp_verify_nonce($nonce, 'wp_rest')) { 
        return new WP_REST_Response([ 'success' => false, 'message' => 'Unauthorized request!' ], 200);
    }
    /*
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if (strpos($referer, home_url()) !== 0) {
        return new WP_REST_Response(['success' => false, 'message' => 'Unauthorized request!'], 200);
    }
    */

    // Get request fields
    $email        = sanitize_email($req->get_param('tw_email'));
    $password     = $req->get_param('tw_password');
    $cpassword    = $req->get_param('tw_cpassword');
    $parentname   = sanitize_text_field($req->get_param('tw_parentname'));
    $country_code = sanitize_text_field($req->get_param('tw_country'));
    $phone        = sanitize_text_field($req->get_param('tw_phone'));
    $phone        = TrimPhoneNo($phone);

    $username     = sanitize_user($phone, true);
    $refid = sanitize_text_field($_SESSION['twrefid'] ?? '');
    // VALIDATION

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors->add("invalid_phone", "Phone number must be 10 digits.");
    } else if (empty($username) || !validate_username($username)) {
        $errors->add("invalid_username", "Invalid username. Do not include space or any special character in your phone number.");
    } else if (username_exists($username)) {
        $errors->add("username_exit", "Phone number already taken. Try another phone number!");
    } else if (!is_email($email)) {
        $errors->add("invalid_username", "Invalid email address.");
    } else if (email_exists($email)) {
        $errors->add("email_exist", "Username already exist. Try another email address!");
    } else if (strlen($password) < 8) {
        $errors->add("wrong_password", "Password must be at least 8 characters.");
    } else if (empty($password)) {
        $errors->add("invalid_password", "Password is required.");
    } else if ($password !== $cpassword) {
        $errors->add("pwd_notmatch", "Password not match!");
    }

    // Return errors if any
    if ($errors->has_errors()) {
        foreach ($errors->get_error_messages() as $error) {
            $msg .= '<p>' . esc_html($error) . '</p>';
        }
        return new WP_REST_Response(['success' => false, 'message' => $msg], 200);
    }

    // USER CREATION ---
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        return new WP_REST_Response(['success' => false, 'message' => $user_id->get_error_message()], 200);
    }

    $user = new WP_User($user_id);
    $user->set_role('student');

    // Split names
    $flname = explode(" ", $parentname);
    $lname = $flname[0];
    $fname = $flname[1] ?? '';
    $dname = !empty($fname) ? $fname : $parentname;

    wp_update_user([
        'ID'           => $user_id,
        'display_name' => $dname,
        'nickname'     => $parentname,
        'first_name'   => $fname,
        'last_name'    => $lname
    ]);

    // Save user meta
    update_user_meta($user_id, 'tw_userdata', [
        'children'    => [],
        'parentname'  => $parentname,
        'countrycode' => $country_code,
        'phone'       => $phone,
        'totalpaid'   => 0.00,
        'regdate'     => time(),
        'refid'       => $refid
    ]);

    // Send emails
    $admin_email = get_option("tw_from_email");

    $subject = 'Your Techwatt Account Confirmation';
    $msg = "Dear ".$parentname.",\n\nYour account has been successfully created. Login with the following details below; \nLogin: https://techwatt.ai/signmein,\nUsername: ".$email." or ".$username."\nPassword: ".$password."\n\nPlease login to add course(s) of your choice. For assistance, do not hesitate to contact us.\n\nBest regards,\nTechwatt Team";

    ScheduleEmail($email, $subject, $msg);

    if ($admin_email != '') {
        ScheduleEmail($admin_email, "New signup on techwatt.ai",
            "Dear admin,\n".$parentname." just created a new account on techwatt.ai. Login to your admin backend to check it out. https://techwatt.ai/backend"
        );
    }

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Registration successful! Click here to <a href="'.esc_url(twUrl("PS_Login")).'">login</a>. Check your email for confirmation message.'
    ], 200);
}

///////////////////////////////////////////////////////////////////////
?>