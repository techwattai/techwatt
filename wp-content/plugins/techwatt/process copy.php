<?php
add_action('wp_ajax_ps_editprofile', 'fxn_ps_editprofile');
add_action('wp_ajax_nopriv_ps_editprofile', 'fxn_ps_editprofile');

function fxn_ps_editprofile() {
global $CountryCodes,$ArrayCourses;
$ck=0; $totalpaid = 0;
$errors = new WP_Error();

//if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ps_edit_profile'])) {
if (!check_ajax_referer('ps_edit_profile', 'security', false) ) {        
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
        if(empty($childid)){ $errors->add("error_childid","Invalid child account, refresh page!"); }
        if(empty($childname)){ $errors->add("error_childid","Your child's name is required."); }
        if(empty($childage)){ $errors->add("error_childid","Your child's age is required"); }
        if(empty($childcourse)){ $errors->add("error_childid","Selected course is required."); }
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
            $msg .= '<p style="color:red;">' . esc_html($error) . '</p>';
        }
        wp_send_json_error( ['message' => $msg], 403 );
    }else{
        $flname = explode(" ",$parentname);
        $lname = $flname[0];
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

        if (!is_wp_error($result)) {         
            $myChildren = get_user_meta($userID, 'children', true); 
            
            foreach($_POST['tw_childid'] as $key=>$valKidID){
                $childid     = sanitize_text_field($valKidID) ?? '';
                $childname   = sanitize_text_field($_POST['tw_childname'][$key]) ?? '';    
                $childage    = intval($_POST['tw_childage'][$key]) ?? '';
                $childcourse = sanitize_text_field($_POST['tw_childcourse'][$key]) ?? '';
                
                $myChildren[$childid]["id"] = $childid;
                $myChildren[$childid]["name"] = $childname;
                $myChildren[$childid]["age"] = $childage;
                $myChildren[$childid]["course"] = $childcourse;
                //$myChildren[$childid]["class"] = $childclass;
                $totalpaid += (float)$myChildren[$childid]["paid"];
            }

            update_user_meta($userID, 'children', $myChildren);
            update_user_meta($userID, 'parentname', $parentname);
            update_user_meta($userID, 'countrycode', $countrycode);
            update_user_meta($userID, 'phone', $phone);
            update_user_meta($userID, 'totalpaid', $totalpaid);

            $msg = 'Profile updated successfully!';
            $user = get_userdata($userID); //wp_get_current_user(); // Refresh data
            $userID = $user->ID;
        }else{
            echo '<p style="color:red;">❌ ' . $result->get_error_message() . '</p>';
        }
    }
}else{
    $msg = 'Request aborted!';
}

}
///////////////////////

?>