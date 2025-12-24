<?php
// Shortcode: Registration Form
add_shortcode("ps_trustpilot",function($atts){
    $atts = shortcode_atts([
        'locale'          => 'en-US',
        'template_id'     => '5419b6a8b0d04a076446a9ad',
        'bizunit_id' => '5f2d1d8d1c3a3c0001e0e4c1',
        'height'          => '50px',
        'width'           => '100px',
        'theme'           => 'light',
        'hdcolor'           => 'black',
        'domain'      => 'techwatt.ai'
    ], $atts, 'ps_trustpilot');

    ob_start();
    ?>
    <div class="trustpilot-widget" data-locale="<?php echo esc_attr($atts['locale']); ?>" data-template-id="<?php echo esc_attr($atts['template_id']); ?>" data-businessunit-id="<?php echo esc_attr($atts['bizunit_id']); ?>" data-style-height="<?php echo esc_attr($atts['height']); ?>" data-style-width="<?php echo esc_attr($atts['width']); ?>" data-theme="<?php echo esc_attr($atts['theme']); ?>">
      <a href="https://www.trustpilot.com/review/<?php echo esc_attr($atts['domain']); ?>" target="_blank" rel="noopener" style="color:<?php echo esc_attr($atts['hdcolor']); ?>;">Trustpilot</a>
    </div><script type="text/javascript" src="https://widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>
<?php
    return ob_get_clean();
});

add_shortcode('twatt_bookform', function () {
    global $CountryCodes;
    $MyCountryCode = PS_GetLocation();
    ob_start(); 
?>
    
    <form class="techwatt-form" method="post" action="<?php echo esc_url(twUrl("PS_TrialReg")); ?>">
        <div class="techwatt-form-grid">
            <div style="flex:3;">
                <label for="tw_phone">Phone Number <small style="color:#666;font-weight:500;">(WhatsApp no. preferred)</small></label>
                <div class="tw-input-group">
                    <!--<input type="text" name="tw_country" id="tw-country" placeholder="+1" required style="width:60px;">-->
                    <select name="tw_country" id="tw_country" required style="width:100px;">
                        <?php foreach ($CountryCodes as $code => $dial): ?>
                            <option value="<?php echo esc_attr($dial); ?>" <?php echo (strtoupper($MyCountryCode) === strtoupper($code))?' selected':''; ?>>
                                <?php echo esc_html($code.' '.$dial); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="tel" name="tw_phone" id="tw_phone" placeholder="1234567890" max=10 required style="flex:1;">
                </div>
            </div>
            <div class="select-age" style="flex:1;">
                <label for="tw_age">Child's Age</label>
                <select name="tw_age" id="tw_age" required>
                <?php for($age = AgeMin; $age<=AgeMax; $age++): ?>
                    <option value="<?php echo esc_attr($age); ?>"><?php echo esc_html('Age '.$age); ?></option>
                    <!--<label class="tw-age-box">
                        <input type="radio" name="tw_age" value="<?php //echo $age; ?>" required>
                        <span>Age <?php //echo $age; ?></span>
                    </label>-->
                <?php endfor; ?>
                </select>
            </div>
        </div>

        <button type="submit" name="tw_submit" class="gradient-3">Book a Trial Class</button>
        <div class="booksplashicons">
        <span class="iconsplash"><i class="bi bi-person-video2 spcolor0"></i>Live 1:1 Classes</span>
        <span class="iconsplash"><i class="bi bi-people spcolor2"></i>For ages 5-16</span>
        <span class="iconsplash"><i class="bi bi-award spcolor4"></i>World-class instructors</span>
        </div>
    </form>

    <?php
    return ob_get_clean();
});

add_shortcode('ps_accmenus', function () {
    $item = '';$uSubMenus = ''; $aSubMenus = '';
    $ddArow = '<span class="dropdown-menu-toggle ast-header-navigation-arrow"><span class="ast-icon icon-arrow"><svg class="ast-arrow-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="26px" height="16.043px" viewBox="57 35.171 26 16.043" enable-background="new 57 35.171 26 16.043" xml:space="preserve"><path d="M57.5,38.193l12.5,12.5l12.5-12.5l-2.5-2.5l-10,10l-10-10L57.5,38.193z"></path></svg></span></span>';

    $studentMenus = [
        ["Dashboard",       twUrl("PS_UDashboard")],
        ["My Profile",      twUrl("PS_UProfile")],
        ["Registered Courses",   twUrl("PS_BookingOrders")],
        ["Kids Projects",   twUrl("PS_KidsProjects")],
        ["LMS",             twUrl("PS_LMS")],
        //["Quizzes",         twUrl("PS_Quizzes")],
        ["Testimonies",     twUrl("PS_Testimonies")],
        ["Change Password", twUrl("PS_UChangePwd")],
        ["Logout",          twUrl("PS_LogOut")],
    ]; 
    
    $adminMenus = [
        ["Dashboard",   admin_url('/')],
        ["Manage Techwatt", admin_url('admin.php?page=techwatt-dashboard')],
        ["My Profile", admin_url('profile.php')],
        ["Manage Users", admin_url('users.php')],
        ["Products",  admin_url('edit.php?post_type=product')],
        ["Pages",  admin_url('edit.php?post_type=page')],
        ["Posts",  admin_url('edit.php')],
        ["Logout", twUrl("PS_LogOut")],
    ];

    if (is_user_logged_in() && current_user_can('student')) {
        foreach($studentMenus as $menu){
            $uSubMenus .= '<li class="mega-menu menu-item menu-item-type-custom menu-item-object-custom"><a href="'.esc_url($menu[1]).'" class="menu-link">'.esc_html($menu[0]).'</a></li> ';
        }
        
        $item = '<ul class="main-header-menu ast-menu-shadow ast-nav-menu ast-flex submenu-with-border astra-menu-animation-slide-down ast-menu-hover-style-zoom ast-cmenus stack-on-mobile"><li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children"><a href="#" class="menu-link parent-menu-link"><i class="bi bi-person-circle pe-2"></i>My'.$ddArow.'</a><ul class="sub-menu csubmenus">'.$uSubMenus.'</ul></li></ul>';

    }elseif(is_user_logged_in() && current_user_can('administrator')){
        foreach($adminMenus as $menu){
            $aSubMenus .= '<li class="mega-menu menu-item menu-item-type-custom menu-item-object-custom"><a href="'.esc_url($menu[1]).'" class="menu-link">'.esc_html($menu[0]).'</a></li> ';
        }
        
        $item = '<ul class="main-header-menu ast-menu-shadow ast-nav-menu ast-flex submenu-with-border astra-menu-animation-slide-down ast-menu-hover-style-zoom ast-cmenus stack-on-mobile"><li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children"><a href="#" class="menu-link parent-menu-link"><i class="bi bi-person-circle pe-2"></i>Admin'.$ddArow.'</a><ul class="sub-menu csubmenus">'.$aSubMenus.'</ul></li></ul>';
    }else{
        $item = '<a href="'.esc_url(twUrl("PS_Login")).'" style="color: #444; font-weight: 500;">Login</a>';
    }
    return $item;
});

//////////////////////////////////////////////

add_shortcode('ps_booktrial', function ($atts) {
    global $CountryCodes,$ArrayCourses, $ArrayPackages;
    $MyCountryCode = PS_GetLocation();

    $atts = shortcode_atts( array( 'userid'=>'0' ), $atts, 'ps_booktrial' );
    $userid = intval( $atts['userid'] );
    $myemail = '';

        if($userid > 0){
            $userID = $userid;
        }else{
            $cuser = wp_get_current_user();
            if(is_user_logged_in() && in_array('student', (array) $cuser->roles)){                
                $userID = $cuser->ID;
                $myemail = $cuser->user_email;
            }else{
                $userID = 0;
            }
        }

        if($userID > 0){
            $tw_userdata = get_user_meta($userID, 'tw_userdata', true);
            $parentName = $tw_userdata['parentname'] ?? '';
            $parentEmail = $myemail ?? '';
            $MyCountryCode = $tw_userdata['countrycode'] ?? $MyCountryCode;
            $phone = $tw_userdata['phone'] ?? '';
        }

        $urlParam = isset($_GET['add-course']);
    ob_start();    
    ?>
    <div id="cmsgbox" style="position:absolute;top:-80px;left:0;z-index:100;background:#fff;padding:20px;border-radius:1px;display:none;min-height:350px;width:100%;text-align:center;"><i class="bi bi-check-circle" style="font-size:40px;color:green;"></i><h2>Confirmation</h2><p style="font-size:18px;">You have successfully book a trial class. Click here to <a href="<?php echo esc_url(twUrl("PS_Login")); ?>">login</a>. Check your email for confirmation message.</p><button class="btn btn-secondary" id="cbtncancel">Cancel</button> <a href="<?php echo esc_url(twUrl("PS_Login")); ?>" class="btn btn-success" id="cbtnlogin">Login</a></div>
    
    <div id="pmsgbox" style="<?php echo ($userID > 0 && $urlParam) ? '':'position:absolute;top:-80px;left:0;z-index:10;'; ?>background:#fff;padding:20px;border-radius:1px;display:none;min-height:350px;width:100%;text-align:center;"><i class="bi bi-check-circle" style="font-size:40px;color:green;"></i><h2>Confirmation</h2><p style="font-size:18px;">You have successfully created a new course. Please check your email for the confirmation message. Click the payment button below to complete your payment.</p><a href="<?php echo esc_url(twUrl("PS_BookingOrders")); ?>" class="btn btn-secondary" id="pbtncancel">Continue</a> <a href="javascript:;" class="btn btn-success pbtnpay" id="stripe-pay-btn" data-amount="" data-currency="gbp" data-uid="" data-childid="" data-name="" data-email="" data-apicall="cstripe-pay">Pay Now<span id="pbtncost"></span></a></div>

    <form method="post" id="psRegFrm" action="<?php echo esc_url( rest_url('techwatt/v1/booktrial') ); ?>">
        <input type="hidden" name="security" id="security" value="<?php wp_create_nonce('wp_rest'); ?>">
        <input type="hidden" name="tw_uid" id="tw_uid" value="<?php echo esc_attr($userID); ?>">

        <div class="techwatt-form-grid">
            <div style="flex:1;">
                <label for="tw_country">Country Code <span class="red">*</span></label>
                <select name="tw_country" id="tw_country" required>
                        <?php foreach ($CountryCodes as $code => $dial): ?>
                            <option value="<?php echo esc_attr($dial); ?>" <?php echo ( (isset($_POST["tw_country"]) && strtolower($_POST["tw_country"]) === strtolower($dial)) || strtolower($MyCountryCode) === strtolower($dial))?' selected':''; ?>>
                                <?php echo esc_html($code.' '.$dial); ?>
                            </option>
                        <?php endforeach; ?>
                </select>
            </div>
            <div style="flex:2;">
                <label for="tw_phone">Phone Number<span class="red">*</span> <small style="color:#666;font-weight:500;">(WhatsApp no. preferred)</small></label>
                <input type="tel" name="tw_phone" id="tw_phone" placeholder="1234567890" max=10 required value="<?php echo $_POST["tw_phone"] ?? ($phone ?? ''); ?>">
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
        
        <div id="packageinfo" style="font-size:0.85em;color:#FE0088;background:#f5f5f5;padding:10px;display:none;margin:-10px 0 10px 0;border-radius:5px;"></div><!--#0033FE-->

        <div class="techwatt-form-grid">              
            <div style="flex:1;">
                <label for="tw_parentname">Parent's Name <span class="red">*</span> <small style="color:#666;font-weight:500;">(Lastname first then firstname)</small></label>
                <input type="text" name="tw_parentname" placeholder="Enter Parent's Name" required value="<?php echo $_POST["tw_parentname"] ?? ($parentName ?? ''); ?>">
            </div>
            <div style="flex:1;">
                <label for="tw_email">Email Address <span class="red">*</span></label>
                <input type="text" name="tw_email" placeholder="Email Address" required value="<?php echo $_POST["tw_email"] ?? ($parentEmail ?? ''); ?>">
            </div>                     
        </div>
        <div class="techwatt-form-grid" style="<?php echo ($userID > 0)?'display:none;':''; ?>">
            <div style="flex:1;">
                <label for="tw_password">Password <span class="red">*</span> <small style="color:#666;font-weight:500;">(min. of 8 xters)</small></label>
                <input type="password" name="tw_password" placeholder="Password">
            </div>  
            <div style="flex:1;">
                <label for="tw_cpassword">Confirm Password <span class="red">*</span></label>
                <input type="password" name="tw_cpassword" placeholder="Confirm Password">
            </div>
        </div>
        <p style="margin:0 0 10px 0;">
            <?php 
                if ($userID > 0){
                    echo 'You are creating a new course as an existing member. <a href="'.esc_url(twUrl("PS_Login")).'">Log out</a> to book with a new account.';
                }else{
                    echo 'Are you already a member? <a href="'.esc_url(twUrl("PS_Login")).'">Login</a> before booking a trial class.';
                }                
            ?>
        </p>
        <p class="ps-m-0"><button type="submit" name="uregister" class="gradient-3">Submit</button></p>
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
            wp_safe_redirect(twUrl("PS_UDashboard"));
        }elseif(current_user_can('administrator')){
            wp_safe_redirect(admin_url());
        }
        exit;
        //return '<p>You are already logged in. <a href="'.twUrl("PS_UDashboard").'">Go to Dashboard</a></p>';
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
                if (in_array('student', (array)$user->roles,true)) {
                    wp_safe_redirect(twUrl("PS_UDashboard"));
                } elseif (in_array('administrator', (array)$user->roles,true)) {
                    wp_safe_redirect(admin_url());
                } else {
                    wp_safe_redirect(home_url());
                }
                exit;
            }
        }
    }
    ?>
    <form method="post" id="psfrm1" class="tw-regfrm">
        <p style="text-align:center;margin:0 0 15px 0;"><?php echo $atts["description"]; ?></p>
        <p><input type="text" name="username" placeholder="Phone Number or Email" required></p>
        <p style="margin:0 0 12px 0;"><input type="password" name="pwd" placeholder="Password" required></p>
        <p style="margin:0 0 5px 0;"><label for="rememberme"><input type="checkbox" name="rememberme" id="rememberme" value="1"> Remember me</label></p>
        <p class="ps-m-0">Are you new, <a href="<?php echo esc_url(twUrl("PS_Register")); ?>">Sign up</a>. Forgot password, <a href="javascript:void(0);" id="tw-forgotpwd">reset</a>?</p>
        <p style="text-align:right;"><button type="submit" name="ulogin">Login</button></p>
    </form>
    <form method="post" id="psfrm2" class="tw-fpwdfrm" style="display:none;" action="<?php echo esc_url( rest_url('techwatt/v1/forgotpwd') ); ?>">
        <p style="text-align:center;margin:0 0 15px 0;">Enter your email address to reset your password.</p>
        <p><input type="email" name="fp_email" placeholder="Email Address" required></p>        
        <p class="ps-m-0">Remembered your password, <a href="javascript:void(0);" id="tw-back2login">login</a>.</p>
        <p style="text-align:right;"><button type="submit" name="fsubmit">Reset Password</button></p>
    </form>
    <script>
        jQuery(document).ready(function($){
            $("#tw-forgotpwd").click(function(){
                $(".tw-regfrm").hide(1200);
                $(".tw-fpwdfrm").show(200);
            });
            $("#tw-back2login").click(function(){
                $(".tw-regfrm").show(200);
                $(".tw-fpwdfrm").hide(100);
            });
        });
    </script>
<?php
    return ob_get_clean();
});

//Register not book trial class..........
add_shortcode('ps_signup', function () {
    global $CountryCodes;
    $MyCountryCode = PS_GetLocation();
    ob_start();    
    ?>
    <div id="cmsgbox" style="position:absolute;top:-80px;left:0;z-index:100;background:#fff;padding:20px;border-radius:1px;display:none;min-height:350px;width:100%;text-align:center;"><i class="bi bi-check-circle" style="font-size:40px;color:green;"></i><h2>Confirmation</h2><p style="font-size:18px;">Registration successful! Click here to <a href="<?php echo esc_url(twUrl("PS_Login")); ?>">login</a> and book a course to enroll. Check your email for confirmation message.</p><button class="btn btn-secondary" id="cbtncancel">Cancel</button> <a href="<?php echo esc_url(twUrl("PS_Login")); ?>" class="btn btn-success" id="cbtnlogin">Login</a></div>
    <?php //echo admin_url('admin-ajax.php'); ?>
    <form method="post" id="psRegFrm" action="<?php echo esc_url( rest_url('techwatt/v1/register') ); ?>">
       
        <input type="hidden" name="security" id="security" value="<?php wp_create_nonce('wp_rest'); ?>">
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

        
        <p style="margin:0 0 10px 0;">Are you already a member? <a href="<?php echo esc_url(twUrl("PS_Login")); ?>">Login</a>.</p>
        <p class="ps-m-0"><button type="submit" name="uregister" class="gradient-3">Register</button></p>
    </form>

<?php
    return ob_get_clean();
});

