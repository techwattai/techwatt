<?php
// Shortcode: Registration Form
// get_user_meta($user_id, 'age', true);
add_shortcode('ps_signup', function () {
    global $CountryCodes,$ArrayCourses, $ArrayPackages;
    $MyCountryCode = PS_GetLocation();
    ob_start();    
    ?>
    <div id="cmsgbox" style="position:absolute;top:-80px;left:0;z-index:100;background:#fff;padding:20px;border-radius:1px;display:none;min-height:350px;width:100%;text-align:center;"><i class="bi bi-check-circle" style="font-size:40px;color:green;"></i><h2>Confirmation</h2><p style="font-size:18px;">Registration successful! Click here to <a href="<?php echo esc_url(PS_Login); ?>">login</a>. Check your email for confirmation message.</p><button class="btn btn-secondary" id="cbtncancel">Cancel</button> <a href="<?php echo esc_url(PS_Login); ?>" class="btn btn-success" id="cbtnlogin">Login</a></div>

    <form method="post" id="psRegFrm" action="<?php echo admin_url('admin-ajax.php'); ?>">
        <?php wp_nonce_field('ps_signup','security'); ?>
        <input type="hidden" name="action" value="ps_signup">

        <div class="techwatt-form-grid">
            <div style="flex:1;">
                <label for="tw_country">Country Code <span class="red">*</span></label>
                <select name="tw_country" id="tw_country" required>
                        <?php foreach ($CountryCodes as $code => $dial): ?>
                            <option value="<?php echo esc_attr($dial); ?>" <?php echo (isset($_POST["tw_country"]) && strtolower($_POST["tw_country"]) === strtolower($dial))?' selected':''; ?>>
                                <?php echo esc_html($code.' '.$dial); ?>
                            </option>
                        <?php endforeach; ?>
                </select>
            </div>
            <div style="flex:2;">
                <label for="tw_phone">Phone Number<span class="red">*</span> <small style="color:#666;font-weight:500;">(WhatsApp no. preferred)</small></label>
                <input type="tel" name="tw_phone" id="tw_phone" placeholder="1234567890" max=10 required value="<?php echo $_POST["tw_phone"] ?? ''; ?>">
            </div>           
        </div>
        <div class="techwatt-form-grid">
            <div class="select-age" style="flex:1;">
                <label for="tw_age">Child's Age <span class="red">*</span></label>
                <select name="tw_age" id="tw_age" required>
                <?php for($age = AgeMin; $age<=AgeMax; $age++): ?>
                    <option value="<?php echo esc_attr($age); ?>" <?php echo (isset($_POST["tw_age"]) &&  strtolower($_POST["tw_age"]) === strtolower($age))?' selected':''; ?>><?php echo esc_html('Age '.$age); ?></option>
                <?php endfor; ?>
                </select>
            </div>
            <div style="flex:2;">
                <label for="tw_childname">Child's Name <span class="red">*</span></label>
                <input type="text" name="tw_childname" placeholder="Enter Child's Name" required value="<?php echo $_POST["tw_childname"] ?? ''; ?>">
            </div>
        </div>
        <div class="techwatt-form-grid">
            <div style="flex:1;">
                <label for="tw_course">Choose a course <span class="red">*</span></label>
                <select name="tw_course" id="tw_course" required><option value=""></option>
                <?php foreach($ArrayCourses as $k=>$v): ?>
                    <option value="<?php echo esc_attr($k); ?>" <?php echo (isset($_POST["tw_course"]) &&  strtolower($_POST["tw_course"]) === strtolower($k))?' selected':''; ?>><?php echo esc_html($v); ?></option>
                <?php endforeach; ?>
                </select>
            </div>
            <div style="flex:1;">
                <label for="tw_package">Choose a package <span class="red">*</span></label>
                <select name="tw_package" id="tw_package" required><option value=""></option>
                <?php foreach($ArrayPackages as $pk=>$pv): ?>
                    <option value="<?php echo strtolower(esc_attr($pk)); ?>" <?php echo (isset($_POST["tw_package"]) &&  strtolower($_POST["tw_package"]) === strtolower($pk))?' selected':''; ?>><?php echo esc_html(ucwords($pk)).' Package ('.DEFAULT_CURRENCY_SYMBOL.esc_html($pv["cost"]).' for '.esc_html($pv["duration"]).' months)'; ?></option>
                <?php endforeach; ?>
                </select>
            </div>
        </div> 
        <div class="techwatt-form-grid">              
            <div style="flex:1;">
                <label for="tw_parentname">Parent's Name <span class="red">*</span> <small style="color:#666;font-weight:500;">(Lastname first then firstname)</small></label>
                <input type="text" name="tw_parentname" placeholder="Enter Parent's Name" required value="<?php echo $_POST["tw_parentname"] ?? ''; ?>">
            </div>
            <div style="flex:1;">
                <label for="tw_email">Email Address <span class="red">*</span></label>
                <input type="text" name="tw_email" placeholder="Email Address" required value="<?php echo $_POST["tw_email"] ?? ''; ?>">
            </div>                     
        </div>
        <div class="techwatt-form-grid">
            <div style="flex:1;">
                <label for="tw_password">Password <span class="red">*</span> <small style="color:#666;font-weight:500;">(min. of 8 xters)</small></label>
                <input type="password" name="tw_password" placeholder="Password" required>
            </div>  
            <div style="flex:1;">
                <label for="tw_cpassword">Confirm Password <span class="red">*</span></label>
                <input type="password" name="tw_cpassword" placeholder="Confirm Password" required>
            </div>
        </div>
        <p style="margin:0 0 10px 0;">Are you already a member? <a href="<?php echo esc_url(PS_Login); ?>">Login</a>.</p>
        <p class="ps-m-0"><button type="submit" name="uregister" class="gradient-3">Register</button></p>
    </form>
<?php
    return ob_get_clean();
});


// Shortcode: Login Form
add_shortcode('ps_signin', function ($atts) {
    $atts = shortcode_atts(['description'=>''], $atts, 'ps_trustpilot');
    /**/
    if (is_user_logged_in() && !isset($_POST['ulogin'])) {
        if(current_user_can('student')){
            wp_safe_redirect(PS_UDashboard);
        }elseif(current_user_can('administrator')){
            wp_safe_redirect(admin_url());
        }
        exit;
        //return '<p>You are already logged in. <a href="'.PS_UDashboard.'">Go to Dashboard</a></p>';
    }
    ob_start();
    if (isset($_POST['ulogin'])) {
        $creds = array();
        $username = sanitize_user($_POST['username']);

        $creds['user_login']    = $username;
        $creds['user_password'] = $_POST['pwd'];
        $creds['remember']      = !empty($_POST['rememberme']) ?? false;

        if(empty($username)){ 
            echo '<p style="color:red;text-align:center;margin:0 0 5px 0;">❌ Username is required!</p>';
        }else{
            $user = wp_signon($creds, false);
            if (is_wp_error($user)) {
                echo '<p style="color:red;text-align:center;margin:0 0 5px 0;">❌ ' . $user->get_error_message() . '</p>';
            } else {
                // Redirect based on role
                if (in_array('student', (array)$user->roles)) {
                    wp_safe_redirect(PS_UDashboard);
                } elseif (in_array('administrator', (array)$user->roles)) {
                    wp_safe_redirect(admin_url());
                } else {
                    wp_safe_redirect(home_url());
                }
                exit;
            }
        }
    }
    ?>
    <form method="post" id="psfrm1">
        <p style="text-align:center;margin:0 0 15px 0;"><?php echo $atts["description"]; ?></p>
        <p><input type="text" name="username" placeholder="Phone Number or Email" required></p>
        <p style="margin:0 0 12px 0;"><input type="password" name="pwd" placeholder="Password" required></p>
        <p style="margin:0 0 5px 0;"><label for="rememberme"><input type="checkbox" name="rememberme" id="rememberme" value="1"> Remember me</label></p>
        <p class="ps-m-0">Are you new, <a href="<?php echo esc_url(PS_Register); ?>">Book a trial class</a>.</p>
        <p style="text-align:right;"><button type="submit" name="ulogin">Login</button></p>
    </form>
<?php
    return ob_get_clean();
});

// My Account Dashboard Shortcode
add_shortcode('ps_portal', function () {
    global $CURRENT_USER;
    $user = $CURRENT_USER; //wp_get_current_user();
    $userID = $user->ID;
    $isLoggedIn = is_user_logged_in();
    //return '<h4 style="margin:2px 0;">Account Restriction!</h4><p>Kindly <a href="'.esc_url(PS_Login).'">login</a> to access your account on techwatt.</p>';    
    if( !$isLoggedIn || ($isLoggedIn && !in_array('student', (array) $user->roles)) ){
        //wp_safe_redirect(PS_Home); //exit;
    }

    ob_start(); 
    ?>
    <div class="account-dashboard">
        <aside>
            <ul>
                <li><a href="<?php echo esc_url(PS_UDashboard); ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li><a href="<?php echo esc_url(PS_UProfile); ?>"><i class="bi bi-person-lines-fill"></i> My Profile</a></li>
                <li><a href="<?php echo esc_url(PS_BookingOrders); ?>"><i class="bi bi-robot"></i> My Booking</a></li>
                <li><a href="<?php echo esc_url(PS_ProductOrders); ?>"><i class="bi bi-robot"></i> Ordered Products</a></li>
                <li><a href="<?php echo esc_url(PS_LMS); ?>"><i class="bi bi-book-half"></i> LMS</a></li>
                <li><a href="<?php echo esc_url(PS_Quizzes); ?>"><i class="bi bi-spellcheck"></i> Quizzes</a></li>
                <li><a href="<?php echo esc_url(PS_KidsProjects); ?>"><i class="bi bi-backpack"></i> Kids Projects</a></li>
                <li><a href="<?php echo esc_url(PS_Testimonies); ?>"><i class="bi bi-megaphone"></i> Testimonies</a></li>
                <li><a href="<?php echo esc_url(PS_UChangePwd); ?>"><i class="bi bi-box-arrow-right"></i> Change Password</a></li>
                <li><a href="<?php echo wp_logout_url(home_url()); ?>"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </aside>
        <main style="flex:1;">
            <?php require "account-templates.php"; ?>            
        </main>
    </div>
    <?php
    return ob_get_clean();
});
?>