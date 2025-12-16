<?php
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