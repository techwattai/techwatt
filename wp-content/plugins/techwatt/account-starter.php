<?php
///////////////////////////////
function PS_MyReferral_Count($uid) {
    if ( ! $uid ) return 0;
    $args = array(
        'meta_key'   => 'refid',
        'meta_value' => $uid,
        'count_total'  => true,   // IMPORTANT!
        'fields'       => 'ID',   // Faster, returns only user IDs
        'number'       => 1
    );
    $user_query = new WP_User_Query( $args );
    return (int) $user_query->get_total();
}
////////////////////////////////
function PS_TrialChildren($children) {
    // Return all children where paymentstatus == "trial"
    $trial_children = array_filter($children, function($child) {
        return isset($child['paymentstatus']) && strtolower($child['paymentstatus']) === 'trial';
    });
    return $trial_children;
}
//////////////////////
function ps_Total_Ordered_Products($uid) {
    if ( ! $uid ) return 0;
    $orders = wc_get_orders(['customer_id' => $uid, 'status' => array_keys(wc_get_order_statuses()),  'limit' => -1,]);
    
    $total_items = 0;
    foreach ( $orders as $order ) {
        foreach ( $order->get_items() as $item ) {
            $total_items += $item->get_quantity();
        }
    }
    return $total_items;
}
////////////
function ps_MostViewed_Products($atts) {
    ob_start();
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 10,
        'meta_key'       => 'views_count',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    );

    $loop = new WP_Query($args);
    if ($loop->have_posts()) {
        echo '<ul class="products most-viewed" style="margin:0;">';
        while ($loop->have_posts()) : $loop->the_post();
            wc_get_template_part('content', 'product');
        endwhile;
        echo '</ul>';
    } else {
        echo '<div style="background:#f5f5f5;border:#eee solid 1px;border-radius:5px;padding:10px;">No popular products yet.</div>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('ps_mostviewed_products', 'ps_MostViewed_Products');

add_shortcode('ps_year', function($atts) {
    $atts = shortcode_atts(['format' => 'Y',], $atts, 'ps_year');
    return date($atts['format']);
});

///////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////// My Account Dashboard Shortcode ///////////////////////

add_shortcode('ps_portal', function () {
    global $CURRENT_USER, $ArrayPackages, $ArrayCourses;
    $user = $CURRENT_USER; //wp_get_current_user();
    $userID = $user->ID;
    $isLoggedIn = is_user_logged_in();
    
    $tw_userdata = get_user_meta($userID, 'tw_userdata',true) ?? []; 
    $children = $tw_userdata['children'] ?? []; 

    $TrialExist = false; $FullTrialExist = false;
    $trialChildren = PS_TrialChildren($children); 
    $trialPopup = '';
    $userName = $user->display_name ?? 'User';
    $userEmail = $user->user_email ?? '';

    $greetings = PS_Greeting($userName);

    //return '<h4 style="margin:2px 0;">Account Restriction!</h4><p>Kindly <a href="'.esc_url(twUrl("PS_Login")).'">login</a> to access your account on techwatt.</p>';    
    //if( !$isLoggedIn || ($isLoggedIn && !in_array('student', (array) $user->roles)) ){
        //wp_safe_redirect(PS_Home); //exit;
    //}

    if( $isLoggedIn && in_array('student', (array) $user->roles) && count($trialChildren) > 0 ){
        $TrialExist = true;
        $FullTrialExist = (count($trialChildren) == count($children)) ? true : false;

        $firstKey = array_key_first($trialChildren); //reset($trialChildren); key($trialChildren); first value & key
        $trialcourse = $trialChildren[$firstKey]['course'] ?? '';
        $trialpackage = $ArrayPackages[$trialChildren[$firstKey]['package']]['name'] ?? '';
        $trialcost = $trialChildren[$firstKey]['cost'] ?? '';
        $trialchildid = $trialChildren[$firstKey]['id'] ?? '';

        $trialPopup = '<div class="trialpopup"><h2>'.$greetings.'</h2><p><b>Welcome to Techwatt Robotics & AI! 🎉</b> Thank you for signing up for our Trial AI Course for Kids. Your child is about to explore the exciting world of Robotics, Coding, and Artificial Intelligence - and we\'re thrilled to have you join our learning community.</p><p><b>👉 Start by watching the introduction video we\'ve prepared for you.</b>';
        
        $trialPopup .= '<br><br><iframe width="560" height="315" src="https://www.youtube.com/embed/yRZ3EVfC-vY" title="Introduction class" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';

        $trialPopup .= '</p><p>It gives a clear overview of what your child will learn and how our program works. To unlock full access, please proceed to complete your course payment. Once payment is confirmed, your child\'s full account access will be activated automatically.</p><p class="alert alert-primary trialinfo"><b>Course Name:</b> '.ucwords($trialcourse).', <b>Package:</b> '.$trialpackage.'</p><a href="javascript:;" class="btn btn-md btn-primary" id="stripe-pay-btn" data-amount="'.trim($trialcost).'" data-currency="gbp" data-childid="'.$trialchildid.'" data-uid="'.$userID.'" data-apicall="cstripe-pay" data-name="'.$userName.'" data-email="'.$userEmail.'">Proceed to Payment ('.PSCurrencySymbol(number_format($trialcost,2)).')</a>
        </div>';
    }

    ob_start(); 
    
    if(isset($_GET['confirm'])){
        include plugin_dir_path(__FILE__) . 'templates/confirmation.php';
        return ob_get_clean();
    }
    ?>
    <div class="account-dashboard" style="position:relative;">
        <?php 
        if($FullTrialExist):
            echo $trialPopup; 
        else:
        ?>
        <aside>
            <ul>
                <li><a href="<?php echo esc_url(twUrl("PS_UDashboard")); ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li><a href="<?php echo esc_url(twUrl("PS_BookingOrders")); ?>"><i class="bi bi-journal-text"></i> Registered Courses</a></li>
                <li><a href="<?php echo esc_url(twUrl("PS_KidsProjects")); ?>"><i class="bi bi-backpack"></i> Kids Projects</a></li>
                <li><a href="<?php echo esc_url(twUrl("PS_ProductOrders")); ?>"><i class="bi bi-robot"></i> Ordered Products</a></li>
                <li><a href="<?php echo esc_url(twUrl("PS_LMS")); ?>"><i class="bi bi-book-half"></i> LMS</a></li>
                <!--<li><a href="<?php //echo esc_url(twUrl("PS_Quizzes")); ?>"><i class="bi bi-spellcheck"></i> Quizzes</a></li>-->
                <li><a href="<?php echo esc_url(twUrl("PS_Testimonies")); ?>"><i class="bi bi-megaphone"></i> Testimonies</a></li>
                <li><a href="<?php echo esc_url(twUrl("PS_MyReferrals")); ?>"><i class="bi bi-people"></i> My Referrals</a></li>
                <li><a href="<?php echo esc_url(twUrl("PS_UProfile")); ?>"><i class="bi bi-person-lines-fill"></i> My Profile</a></li>
                <li><a href="<?php echo esc_url(twUrl("PS_UChangePwd")); ?>"><i class="bi bi-box-arrow-right"></i> Change Password</a></li>
                <li><a href="<?php echo esc_url(twUrl("PS_LogOut")); ?>"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul><!--wp_logout_url(home_url());-->
        </aside>
        <main style="flex:1;">
            <?php 
                if(isset($_GET['profile'])){
                    include plugin_dir_path(__FILE__) . 'templates/profile.php';
                }else if(isset($_GET['chgpwd'])){
                    include plugin_dir_path(__FILE__) . 'templates/change-pwd.php';
                }else if(isset($_GET['quizzes'])){
                    include plugin_dir_path(__FILE__) . 'templates/quizzes.php';
                }else if(isset($_GET['booking-order'])){
                    include plugin_dir_path(__FILE__) . 'templates/booking-order.php';
                }else if(isset($_GET['product-order'])){
                    include plugin_dir_path(__FILE__) . 'templates/product-order.php';
                }else if(isset($_GET['kids-projects'])){
                    include plugin_dir_path(__FILE__) . 'templates/kids-projects.php';
                }else if(isset($_GET['add-course'])){
                    include plugin_dir_path(__FILE__) . 'templates/add-course.php';
                }else if(isset($_GET['add-project'])){
                    include plugin_dir_path(__FILE__) . 'templates/kid-addproject.php';
                }else if(isset($_GET['edit-project'])){
                    include plugin_dir_path(__FILE__) . 'templates/kid-editproject.php';
                }else if(isset($_GET['testimonies'])){
                    include plugin_dir_path(__FILE__) . 'templates/testimonies.php';
                }else if(isset($_GET['add-testimony'])){
                    include plugin_dir_path(__FILE__) . 'templates/testimony-add.php';
                }else if(isset($_GET['myreferrals'])){
                    include plugin_dir_path(__FILE__) . 'templates/referrals.php';
                }else{
                    include plugin_dir_path(__FILE__) . 'templates/dashboard.php';
                }
            ?>            
        </main>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
});

////////////////////////
