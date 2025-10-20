<?php
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
    
    <form class="techwatt-form" method="post" action="<?php echo esc_url(PS_Register); ?>">
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
        ["Dashboard",       PS_UDashboard],
        ["My Profile",      PS_UProfile],
        ["My Booking",   PS_BookingOrders],
        ["Kids Projects",   PS_KidsProjects],
        ["LMS",             PS_LMS],
        ["Quizzes",         PS_Quizzes],
        ["Testimonies",     PS_Testimonies],
        ["Change Password", PS_UChangePwd],
        ["Logout",          PS_LogOut],
    ]; 
    
    $adminMenus = [
        ["Dashboard",   admin_url('/')],
        ["Techwatt Panel", '#'],
        ["My Profile", admin_url('profile.php')],
        ["Manage Users", admin_url('users.php')],
        ["Products",  admin_url('edit.php?post_type=product')],
        ["Pages",  admin_url('edit.php?post_type=page')],
        ["Posts",  admin_url('edit.php')],
        ["Logout", PS_LogOut],
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
        $item = '<a href="'.esc_url(PS_Login).'" style="color: #444; font-weight: 500;">Login</a>';
    }
    return $item;
});

///////////////////

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