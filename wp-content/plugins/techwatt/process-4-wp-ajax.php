<?php
////////////// REGISTER //////////////////////////
add_action('wp_ajax_ps_register', 'fxn_ps_register');
add_action('wp_ajax_nopriv_ps_register', 'fxn_ps_register');

function fxn_ps_register() {
    $errors = new WP_Error();
    if (!check_ajax_referer('ps_register', 'security', false) ) {
        wp_send_json_error(['message' => 'Unauthorized request!']); return;
    }

    $email        = sanitize_email($_POST['tw_email']);
    $password     = $_POST['tw_password'];
    $cpassword    = $_POST['tw_cpassword'];
    $parentname   = sanitize_text_field($_POST['tw_parentname']);
    $country_code = sanitize_text_field($_POST['tw_country']);
    $phone        = sanitize_text_field($_POST['tw_phone']);
    $phone = TrimPhoneNo($phone);
    $username     = sanitize_user($phone,true);
    
    // ✅ Validation: ensure phone is exactly 10 digits
        if (!preg_match('/^[0-9]{10}$/', $phone)) {
            $errors->add("invalid_phone", "Phone number must be 10 digits.");
        }else if(empty($username) || !validate_username($username)){
            $errors->add("invalid_username", "Invalid username. Do not include space or any special character in your phone number.");
        }else if(username_exists($username)){
            $errors->add("username_exit", "Phone number already taken. Try another phone number!");
        }else if(is_email($email) === false){ //return sanitized email or false
            $errors->add("invalid_username", "Invalid email address.");
        }else if(email_exists($email)){
            $errors->add("email_exist", "Username already exist. Try another email address!");
        }else if(strlen($password) < 8) {
            $errors->add("wrong_password", "Password must be at least 8 characters.");
        }else if(empty($password)){
            $errors->add("invalid_password", "Password is required.");
        }else if($password !== $cpassword){
            $errors->add("pwd_notmatch", "Password not match!");
        }
    
    if($errors->has_errors()){
            foreach ($errors->get_error_messages() as $error) {
                $msg .= '<p>' . esc_html($error) . '</p>';
            }
            wp_send_json_error(['message' => $msg]); return;
        }else{
            // Create user....
            $user_id = wp_create_user($username, $password, $email);
            $regdate = time();

            if (!is_wp_error($user_id)) {
                $user = new WP_User($user_id);
                $user->set_role('student');

                $flname = explode(" ",$parentname);
                $lname = $flname[0];
                $fname = $flname[1] ?? '';
                $dname = !empty($fname) ? $fname : $parentname;

                wp_update_user(['ID'=>$user_id,'display_name' => $dname,'nickname' => $parentname,'first_name' => $fname,'last_name' => $lname]); //nickname,firstname, lastname
                // Save extra fields as user meta
                
                update_user_meta($user_id, 'tw_userdata', [
                    'children' => [],
                    'parentname' => $parentname,
                    'countrycode' => $country_code,
                    'phone' => $phone,
                    'totalpaid' => 0.00,
                    'regdate' => $regdate
                ]);

                // Send welcome email
                $admin_email = get_option("tw_from_email");
                $subject = 'Your Techwatt Account Confirmation';
                $msg = "Dear ".$parentname.",\n\nYour account has been successfully created. Login with the following details below; \nLogin: https://techwatt.ai/signmein,\nUsername: ".$email." or ".$username."\nPassword: ".$password."\n\nPlease login to add course(s) of your choice. For assistance, do not hesitate to contact us.\n\nBest regards,\nTechwatt Team";

                ScheduleEmail($email,$subject,$msg);
                if($admin_email != ''){
                    ScheduleEmail($admin_email,"New signup on techwatt.ai","Dear admin,\n".$parentname." just created a new account on techwatt.ai. Login to your admin backend to check it out. https://techwatt.ai/backend");
                }

                wp_send_json_success(['message' => 'Registration successful! Click here to <a href="'.esc_url(PS_Login).'">login</a>. Check your email for confirmation message.']); return;
            } else {
                //echo '<p style="color:red;">❌ ' . $user_id->get_error_message() . '</p>';
                wp_send_json_error(['message' => $user_id->get_error_message()]); return;
            }
        }

}

////////////// SIGN UP - BOOKING A TRIAL CLASS //////////////////////////
add_action('wp_ajax_ps_bktrial', 'fxn_ps_bktrial');
add_action('wp_ajax_nopriv_ps_bktrial', 'fxn_ps_bktrial');

function fxn_ps_bktrial() {
    $errors = new WP_Error();
    if (!check_ajax_referer('ps_bktrial', 'security', false) ) {
        wp_send_json_error(['message' => 'Unauthorized request!']); return;
    }

        global $ArrayPackages;       
        // Sanitize inputs
        $email        = sanitize_email($_POST['tw_email']);
        $password     = $_POST['tw_password'];
        $cpassword    = $_POST['tw_cpassword'];
        $childname    = sanitize_text_field($_POST['tw_childname']);
        $parentname   = sanitize_text_field($_POST['tw_parentname']);
        $country_code = sanitize_text_field($_POST['tw_country']);
        $phone        = sanitize_text_field($_POST['tw_phone']);
        $phone = TrimPhoneNo($phone);
        
        $username     = sanitize_user($phone,true);
        $age          = sanitize_text_field($_POST['tw_age']);
        $course       = sanitize_text_field($_POST['tw_course']);
        $package = sanitize_text_field($_POST["tw_package"]);
        $cost = sanitize_text_field($ArrayPackages[$package]["cost"]);
        $duration = sanitize_text_field($ArrayPackages[$package]["duration"]);

        // ✅ Validation: ensure phone is exactly 10 digits
        if (!preg_match('/^[0-9]{10}$/', $phone)) {
            $errors->add("invalid_phone", "Phone number must be 10 digits.");
        }else if(empty($username) || !validate_username($username)){
            $errors->add("invalid_username", "Invalid username. Do not include space or any special character in your phone number.");
        }else if(username_exists($username)){
            $errors->add("username_exit", "Phone number already taken. Try another phone number!");
        }else if(is_email($email) === false){ //return sanitized email or false
            $errors->add("invalid_username", "Invalid email address.");
        }else if(email_exists($email)){
            $errors->add("email_exist", "Username already exist. Try another email address!");
        }else if(strlen($password) < 8) {
            $errors->add("wrong_password", "Password must be at least 8 characters.");
        }else if(empty($password)){
            $errors->add("invalid_password", "Password is required.");
        }else if($password !== $cpassword){
            $errors->add("pwd_notmatch", "Password not match!");
        }else if(empty($course)){
            $errors->add("empty_course", "Choose a course to enrol!");
        }else if(empty($package)){
            $errors->add("empty_package", "Choose a package to enrol!");
        }
        
        if($errors->has_errors()){
            foreach ($errors->get_error_messages() as $error) {
                $msg .= '<p>' . esc_html($error) . '</p>';
            }
            wp_send_json_error(['message' => $msg]); return;
        }else{
            // Create user....
            $user_id = wp_create_user($username, $password, $email);
            $regdate = time();

            $child_id = strtolower('tw-' . wp_generate_password(6, false, false));
            $children[$child_id] = ["id"=>$child_id,'name' => $childname,'age' => $age, 'course'=>$course, 'class' => '', 'regdate' => $regdate, 'package' => $package, 'cost' => $cost, 'paid' => '0', 'duration' => $duration, 'paymentstatus' => 'Trial'];

            if (!is_wp_error($user_id)) {
                $user = new WP_User($user_id);
                $user->set_role('student');

                $flname = explode(" ",$parentname);
                $lname = $flname[0];
                $fname = $flname[1] ?? '';
                $dname = !empty($fname) ? $fname : $parentname;

                wp_update_user(['ID'=>$user_id,'display_name' => $dname,'nickname' => $parentname,'first_name' => $fname,'last_name' => $lname]); //nickname,firstname, lastname
                // Save extra fields as user meta
                
                update_user_meta($user_id, 'tw_userdata', [
                    'children' => $children,
                    'parentname' => $parentname,
                    'countrycode' => $country_code,
                    'phone' => $phone,
                    'totalpaid' => 0.00,
                    'regdate' => $regdate
                ]);

                // Send welcome email
                $admin_email = get_option("tw_from_email");
                $subject = 'Techwatt Trial Class Booked';
                $msg = "Dear ".$parentname.",\n\nYour techwatt trial class has been successfully created. Login with the following details below; \nLogin: https://techwatt.ai/signmein,\nUsername: ".$email." or ".$username."\nPassword: ".$password."\n\nFor assistance, do not hesitate to contact us.\n\nBest regards,\nTechwatt Team";

                ScheduleEmail($email,$subject,$msg);
                if($admin_email != ''){
                    ScheduleEmail($admin_email,"New techwatt trial class booked","Dear admin,\n".$parentname." just booked a trial class on techwatt.ai. Login to your admin backend to check it out. https://techwatt.ai/backend");
                }

                wp_send_json_success(['message' => 'Trial class was successfully booked! Click <a href="'.esc_url(PS_Login).'">login</a> to access the course. Don\'t forget to check your email for confirmation message.']); return;
            } else {
                //echo '<p style="color:red;">❌ ' . $user_id->get_error_message() . '</p>';
                wp_send_json_error(['message' => $user_id->get_error_message()]); return;
            }
        }
}

////////////// CHANGE PWD AJAX //////////////////////////
add_action('wp_ajax_ps_chgpwd', 'fxn_ps_chgpwd');

function fxn_ps_chgpwd() {
    $errors = new WP_Error();
    if (!check_ajax_referer('ps_chgpwd', 'security', false) ) {
        wp_send_json_error(['message' => 'Unauthorized request!'], 403); return;
    }

    $userID = get_current_user_id();
    $pwd  = sanitize_text_field($_POST['tw_pwd'] ?? '');
    $cpwd = sanitize_text_field($_POST['tw_cpwd'] ?? '');

    if (!$userID) { wp_send_json_error(['message' => 'You must log in to change password.'], 403); return; }

    if(empty($pwd) || empty($cpwd)){
        wp_send_json_error(['message' => 'Both password is required.']); return;
    }else if($pwd !== $cpwd){
        wp_send_json_error(['message' => 'Password do not match!']); return;
    }
    //wp_set_password($pwd, $userID); //will log user out after pwd changed
    wp_update_user(['ID' => $userID, 'user_pass' => $pwd]); //wouldn't log out user...
    wp_send_json_success(['message' => 'Password updated successfully!']); return;
}

//////// EDIT PROFILE AJAX ////////////////////////
add_action('wp_ajax_ps_editprofile', 'fxn_ps_editprofile');
//add_action('wp_ajax_nopriv_ps_editprofile', 'fxn_ps_editprofile');

function fxn_ps_editprofile() {
    global $CountryCodes,$ArrayCourses, $ArrayPackages;
    $totalpaid = 0;  $errors = new WP_Error();

    if ( ! check_ajax_referer('ps_edit_profile', 'security', false) ) {
        wp_send_json_error(['message' => 'Unauthorized request!'], 403);   return;
    }

    //if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ps_edit_profile'])) {      
    $userID     = sanitize_text_field($_POST['tw_userid']);
    $email        = sanitize_email($_POST['tw_email']);
    $parentname     = sanitize_text_field($_POST['tw_parentname']);
    $display_name     = sanitize_text_field($_POST['display_name']);
    $countrycode = sanitize_text_field($_POST['countrycode']);
    $phone        = sanitize_text_field($_POST['tw_phone']);
    $username     = sanitize_user($phone,true);

    $exist_userID = username_exists($username);
    $exist_emailUserID = email_exists($email);

    foreach($_POST['tw_childid'] as $key=>$valKidID){
        $childid     = sanitize_text_field($valKidID) ?? '';
        $childname   = sanitize_text_field($_POST['tw_childname'][$key]) ?? '';    
        $childage    = intval($_POST['tw_childage'][$key]) ?? '';
        $childcourse = sanitize_text_field($_POST['tw_childcourse'][$key]) ?? '';
        $childpackage = sanitize_text_field($_POST["tw_childpackage"][$key]) ?? '';
        //$cost = sanitize_text_field($ArrayPackages[$package]["cost"]);
        //$duration = sanitize_text_field($ArrayPackages[$package]["duration"]);

        if(empty($childid)){ $errors->add("error_childid","Invalid child account, refresh page!"); }
        if(empty($childname)){ $errors->add("error_childid","Your child's name is required."); }
        if(empty($childage)){ $errors->add("error_childid","Your child's age is required"); }
        if(empty($childcourse)){ $errors->add("error_childid","Course is required."); }
        if(empty($childpackage)){ $errors->add("error_childid","Package is required."); }
    }

    if(empty($userID)){
        $errors->add("invalid_userid", "Invalid user account.");
    }else if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors->add("invalid_phone", "Phone number must be 10 digits.");
    }else if(empty($username) || !validate_username($username)){
        $errors->add("invalid_username", "Invalid username. Do not include space or any special character in your phone number.");
    }else if($exist_userID && $exist_userID != $userID){
        $errors->add("username_exit", "Phone number already taken. Try another phone number!");
    }else if(is_email($email) === false){ //return sanitized email or false
        $errors->add("invalid_email", "Invalid email address.");
    }else if($exist_emailUserID && $exist_emailUserID != $userID){
        $errors->add("email_exist", "Username already exist. Try another email address!");
    }

    if($errors->has_errors()){
        foreach ($errors->get_error_messages() as $error) {
            $msg .= '<p>' . esc_html($error) . '</p>';
        }
        wp_send_json_error( ['message' => $msg] );
        return;
    }

        $flname = explode(" ",$parentname);
        $lname = $flname[0] ?? '';
        $fname = $flname[1] ?? '';
        $dname = !empty($display_name) ? $display_name : $parentname;

        $userdata = [
            'ID'           => intval($userID),
            'display_name' => $dname,
            'nickname'     => $parentname,
            'first_name'   => $fname,
            'last_name'    => $lname,
            'user_email'   => $email,
        ];

        $result = wp_update_user($userdata);

        if (is_wp_error($result)) {    
            $msg .= '<p>'.$result->get_error_message().'</p>';
            wp_send_json_error( ['message' => $msg] );
            return;
        }

        $tw_userdta = get_user_meta($userID, 'tw_userdata', true) ?? [];
        $myChildren = $tw_userdta['children'] ?? []; //?:
            
            foreach($_POST['tw_childid'] as $key=>$valKidID){
                $childid     = sanitize_text_field($valKidID) ?? '';
                $childname   = sanitize_text_field($_POST['tw_childname'][$key]) ?? '';    
                $childage    = intval($_POST['tw_childage'][$key]) ?? 0;
                $childcourse = sanitize_text_field($_POST['tw_childcourse'][$key]) ?? '';
                //$defaultcost = $ArrayCourseCost[strtolower($childcourse)]["cost"];
                $newpackage = sanitize_text_field($_POST["tw_childpackage"][$key]) ?? '';
                $newcost = sanitize_text_field($ArrayPackages[$newpackage]["cost"]);
                $newduration = sanitize_text_field($ArrayPackages[$newpackage]["duration"]);
                $paid = $myChildren[$childid]['paid'] ?? '0';
                $class = $myChildren[$childid]['class'] ?? '';
                $paymentstatus = $myChildren[$childid]['paymentstatus'] ?? '';
                $regdate = $myChildren[$childid]['regdate'] ?? time();

                if(strtolower($newpackage) === strtolower($myChildren[$childid]['package'])){
                    $cost = $myChildren[$childid]['cost'] ?? $newcost;
                    $package = $myChildren[$childid]['package'] ?? $newpackage;
                    $duration = $myChildren[$childid]['duration'] ?? $newduration;                    
                }else{
                    $cost = $newcost;
                    $package = $newpackage;
                    $duration = $newduration;
                }
                
                $myChildren[$childid] = [
                    "id" => $childid,
                    "name" => $childname,
                    "age" => $childage,
                    "course" => $childcourse,
                    "class" => $class,
                    "cost" => $cost,
                    "paid" => $paid,
                    "package" => $package,
                    "duration" => $duration,
                    "paymentstatus" => $paymentstatus,
                    "regdate" => $regdate,
                ];
               $totalpaid += (float)$myChildren[$childid]["cost"];              
            }

            $tw_userdta['children'] = $myChildren;
            $tw_userdta['parentname'] = $parentname;
            $tw_userdta['countrycode'] = $countrycode;
            $tw_userdta['phone'] = $phone;
            $tw_userdta['totalpaid'] = $totalpaid;

            update_user_meta($user_id, 'tw_userdata', $tw_userdta);

            wp_send_json_success(['message' => 'Profile updated successfully!']); return;
}

/////////// TESTIMONIAL AJAX ////////////
add_action( 'wp_ajax_tm_form', 'tm_submit_form' );
add_action( 'wp_ajax_nopriv_tm_form', 'tm_submit_form' );

function tm_submit_form() {
   
    if ( ! check_ajax_referer('tm_form_action', 'tm_form_nonce', false) ) {
        wp_send_json_error(['message' => 'Unauthorized request!']);   return;
    }

    $post_id = intval($_POST['postid']);
    $title = sanitize_text_field($_POST['tm_title']);
    $author_name = sanitize_text_field($_POST['kid_name']) ?? 'Guest';
	$content = wp_kses_post($_POST['tm_content']);
	$status = current_user_can('publish_posts') ? 'publish' : 'pending';

    if(empty($title)){ wp_send_json_error(['message' => 'Title of your testimony is required']); return; }
    if(empty($content)){ wp_send_json_error(['message' => 'Content of your testimony is required']); return; }

		if ($post_id) {
			wp_update_post(array(
				'ID' => $post_id,
				'post_title' => $title,
				'post_content' => $content,
				'post_status' => $status,
			));
            update_post_meta($post_id, 'author_name', $author_name);
			$msg = 'Testimony updated successfully.';
		} else {
			$new_post_id = wp_insert_post(array(
				'post_title' => $title,
				'post_content' => $content,
				'post_type' => 'testimony',
				'post_status' => $status,
				'post_author' => $current_user->ID ?? 0,
			));
            if (!is_wp_error($new_post_id)) {
                update_post_meta($new_post_id, 'author_name', $author_name);
            }
			$msg = 'Thank you! Your testimony was submitted<br>successfully and is awaiting review.';
		}
        wp_send_json_success(['message' => $msg]); return;
}
?>