<?php
//error_reporting(0);
define("AgeMin", 5);
define("AgeMax", 16);
define("DEFAULT_CURRENCY_SYMBOL", '£');
define("DEFAULT_CURRENCY", 'GBP');

$ArrayCourses = [
    "robotics"=>"Robotics",
    "python4ai"=>"Python for AI",
    "cv4kids"=>"Computer vision for Kids",
    "ai4kids"=>"AI for Kids",
    "dataanalysis"=>"Introduction to Data Analysis",
    "gameprog"=>"Game Programming",
    "webdesign"=>"Website Designing"
];
$ArrayPackages = ["bronze"=>["name"=>"Bronze For Robotics","cost"=>300,"duration"=>"4","info"=>"Perfect for self-starters,No free kits, up to 30 students in class, community-based learning experience"],"silver"=>["name"=>"Silver For Robotics","cost"=>500,"duration"=>"4","info"=>"Free Robotic kits included, up to 20 students in class, ideal for learners who enjoy collaboration and teamwork"],"gold"=>["name"=>"Gold For Robotics","cost"=>700,"duration"=>"4","info"=>"Free Robotic kits included, one-on-one training sessions twice a week, Access to all previous class recording, offers complete robotics experience"]];

$ArrayCourseCost = [ "robotics"=>["cost"=>450,"duration"=>"month","currency"=>"gbp"],
    "python4ai"=>["cost"=>650,"duration"=>"month","currency"=>"gbp"],
    "cv4kids"=>["cost"=>350,"duration"=>"month","currency"=>"gbp"],
    "ai4kids"=>["cost"=>450,"duration"=>"month","currency"=>"gbp"],
    "dataanalysis"=>["cost"=>400,"duration"=>"month","currency"=>"gbp"],
    "gameprog"=>["cost"=>600,"duration"=>"month","currency"=>"gbp"],
    "webdesign"=>["cost"=>630,"duration"=>"month","currency"=>"gbp"] ];

$CountryCodes = array(
    "GB" => "+44",   // United Kingdom
    "US" => "+1",    // United States
    "NG" => "+234",  // Nigeria
    "GH" => "+233",  // Ghana
    "CA" => "+1",    // Canada
    "ZA" => "+27",   // South Africa
    "AF" => "+93",   // Afghanistan
    "AL" => "+355",  // Albania
    "DZ" => "+213",  // Algeria
    "AS" => "+1-684",// American Samoa
    "AD" => "+376",  // Andorra
    "AO" => "+244",  // Angola
    "AR" => "+54",   // Argentina
    "AM" => "+374",  // Armenia
    "AU" => "+61",   // Australia
    "AT" => "+43",   // Austria
    "AZ" => "+994",  // Azerbaijan
    "BH" => "+973",  // Bahrain
    "BD" => "+880",  // Bangladesh
    "BY" => "+375",  // Belarus
    "BE" => "+32",   // Belgium
    "BZ" => "+501",  // Belize
    "BJ" => "+229",  // Benin
    "BT" => "+975",  // Bhutan
    "BO" => "+591",  // Bolivia
    "BA" => "+387",  // Bosnia and Herzegovina
    "BW" => "+267",  // Botswana
    "BR" => "+55",   // Brazil
    "BN" => "+673",  // Brunei
    "BG" => "+359",  // Bulgaria
    "KH" => "+855",  // Cambodia
    "CM" => "+237",  // Cameroon
    "CL" => "+56",   // Chile
    "CN" => "+86",   // China
    "CO" => "+57",   // Colombia
    "CR" => "+506",  // Costa Rica
    "HR" => "+385",  // Croatia
    "CU" => "+53",   // Cuba
    "CY" => "+357",  // Cyprus
    "CZ" => "+420",  // Czech Republic
    "DK" => "+45",   // Denmark
    "DO" => "+1-809",// Dominican Republic
    "EC" => "+593",  // Ecuador
    "EG" => "+20",   // Egypt
    "EE" => "+372",  // Estonia
    "ET" => "+251",  // Ethiopia
    "FI" => "+358",  // Finland
    "FR" => "+33",   // France
    "GE" => "+995",  // Georgia
    "DE" => "+49",   // Germany
    "GR" => "+30",   // Greece
    "GT" => "+502",  // Guatemala
    "HK" => "+852",  // Hong Kong
    "HU" => "+36",   // Hungary
    "IS" => "+354",  // Iceland
    "IN" => "+91",   // India
    "ID" => "+62",   // Indonesia
    "IR" => "+98",   // Iran
    "IQ" => "+964",  // Iraq
    "IE" => "+353",  // Ireland
    "IL" => "+972",  // Israel
    "IT" => "+39",   // Italy
    "JP" => "+81",   // Japan
    "JO" => "+962",  // Jordan
    "KZ" => "+7",    // Kazakhstan
    "KE" => "+254",  // Kenya
    "KR" => "+82",   // South Korea
    "KW" => "+965",  // Kuwait
    "LV" => "+371",  // Latvia
    "LB" => "+961",  // Lebanon
    "LY" => "+218",  // Libya
    "LT" => "+370",  // Lithuania
    "LU" => "+352",  // Luxembourg
    "MY" => "+60",   // Malaysia
    "MV" => "+960",  // Maldives
    "MT" => "+356",  // Malta
    "MX" => "+52",   // Mexico
    "MD" => "+373",  // Moldova
    "MC" => "+377",  // Monaco
    "MA" => "+212",  // Morocco
    "MM" => "+95",   // Myanmar
    "NP" => "+977",  // Nepal
    "NL" => "+31",   // Netherlands
    "NZ" => "+64",   // New Zealand
    "NO" => "+47",   // Norway
    "OM" => "+968",  // Oman
    "PK" => "+92",   // Pakistan
    "PA" => "+507",  // Panama
    "PY" => "+595",  // Paraguay
    "PE" => "+51",   // Peru
    "PH" => "+63",   // Philippines
    "PL" => "+48",   // Poland
    "PT" => "+351",  // Portugal
    "QA" => "+974",  // Qatar
    "RO" => "+40",   // Romania
    "RU" => "+7",    // Russia
    "SA" => "+966",  // Saudi Arabia
    "RS" => "+381",  // Serbia
    "SG" => "+65",   // Singapore
    "SK" => "+421",  // Slovakia
    "SI" => "+386",  // Slovenia
    "ES" => "+34",   // Spain
    "LK" => "+94",   // Sri Lanka
    "SE" => "+46",   // Sweden
    "CH" => "+41",   // Switzerland
    "TW" => "+886",  // Taiwan
    "TH" => "+66",   // Thailand
    "TR" => "+90",   // Turkey
    "UA" => "+380",  // Ukraine
    "AE" => "+971",  // United Arab Emirates
    "UY" => "+598",  // Uruguay
    "UZ" => "+998",  // Uzbekistan
    "VE" => "+58",   // Venezuela
    "VN" => "+84",   // Vietnam
    "YE" => "+967",  // Yemen
    "ZM" => "+260",  // Zambia
    "ZW" => "+263"   // Zimbabwe
);


function PS_Status($str='Paid', $type = 'info') {
    $class = 'notice-info';
    if($type == 'success' || $type == 'succeed' || $type == 'paid'){ $str = '<span class="badge-success">'.$str.'</span>'; }
    elseif($type == 'secondary' || $type == 'pending'){ $str = '<span class="badge-secondary">'.$str.'</span>'; }
    elseif($type == 'danger' || $type == 'failed' || $type == 'error'){ $str = '<span class="badge-danger">'.$str.'</span>'; }
    else{ $str = '<span class="badge-'.$type.'">'.$str.'</span>'; }
    return $str;
}

function GenerateUID($prefix='BC-', $length = 8) {
    return $prefix.wp_generate_password($length, false, false);
}
/////////////////////////
function SetDropDown($lists=[],$label='<i class="bi bi-three-dots-vertical"></i>'){
    $alists = '';
    foreach($lists as $list){
        $alists .= ($list[0] == '-')?'<li><hr class="dropdown-divider"></li>':'<li><a class="dropdown-item" href="'.$list[1].'"'.(!empty($list[2]) ? ' onClick="'.$list[2].'"':'').'>'.$list[0].'</a></li>';
    }
    return '<div class="btn-group"><button class="btn btn-primary btn-xs dropdown-toggle no-caret" type="button" data-bs-toggle="dropdown">'.$label.'</button><ul class="dropdown-menu">'.$alists.'</ul></div>';
}
///////////////////
function PS_SendMail($to, $subject, $message) {
    /*$headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: Techwatt Team <info@techwatt.ai>'
                );
    return wp_mail($to, $subject, $message, $headers);*/

    if($to != '' && $subject != '' && $message != ''){
         wp_schedule_single_event(time() + 10, 'tw_send_welcome_mail', ['email'=>$to,'subject'=>$subject,'msg'=>$message]);
    }
}   
////////////////////////////////////
function PS_GetCurrentUrl(){
    global $wp;
    $current_url = home_url( add_query_arg( array(), $wp->request ) );
    return $current_url;
}
////////////////////////////////////
function PS_GetLocation() {
    $file = plugin_dir_path(__FILE__) . '/assets/cache.json';
    $cache = [];
    if (file_exists($file)) { $cache = json_decode(file_get_contents($file), true); }
    $countryCode = ''; $now = time(); $countryName = '';

    if (!empty($cache['country_code']) && !empty($cache['timestamp']) && ($now - $cache['timestamp']) < 3600) {
        $countryCode = $cache['country_code'];
        $countryName = $cache['country'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        $resp = wp_remote_get("https://ipapi.co/{$ip}/json/");
        
        if (!is_wp_error($resp) && wp_remote_retrieve_response_code($resp) === 200) {
            $data = json_decode(wp_remote_retrieve_body($resp), true);

            if (!empty($data['country_code'])) {
                $countryCode = strtoupper($data['country_code']);
                $countryName = strtoupper($data['country_name']);
                $cache = ['country' => $countryName, 'country_code' => $countryCode, 'timestamp' => $now];
                file_put_contents($file, wp_json_encode($cache, JSON_PRETTY_PRINT));
            }
        }
    }
    return $countryCode;
}
///////////////
function PS_Greeting( $name = '' ) {
    $hour = (int) current_time('H'); 
    if ( $hour >= 5 && $hour < 12 ) {
        $greeting = 'Good morning';
    } elseif ( $hour >= 12 && $hour < 18 ) {
        $greeting = 'Good afternoon';
    } else {
        $greeting = 'Good evening';
    }

    // Add user name if provided
    if ( !empty($name) ) {
        $greeting .= ', ' . esc_html($name);
    }

    return $greeting . '!';
}
///////////////////
function PSCurrencySymbol($price){
    $currency_symbol = get_woocommerce_currency_symbol();
    return $currency_symbol.$price;
}
function PSCurrency($price){
    $currency = get_woocommerce_currency();
    $currency_symbol = get_woocommerce_currency_symbol();
    return (!empty($price))? '('.$currency.')'.$currency_symbol.$price:'('.$currency.')'.$currency_symbol;
}
///////Pagination Function//////////////////
function Paginator($steps = 1,$rowPerPage=20,$total_pages=0,$currentPage=0){
    if($steps == 1){
        $page_slug = sanitize_text_field($_GET['page'] ?? '');
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $users_per_page = $rowPerPage;
        return ['offset' => ($current_page - 1) * $users_per_page, 'limit' => $users_per_page, 'current_page' => $current_page, 'page_slug' => $page_slug];
    }elseif($steps == 2){
        if ($total_pages > 1) {
            $output = '<div class="tablenav" style="width:98%;"><div class="tablenav-pages custom-pagination">';
            $output .= paginate_links(array(
                'base'      => add_query_arg('paged', '%#%'),
                'format'    => '',
                'prev_text' => __('« Prev'),
                'next_text' => __('Next »'),
                'total'     => $total_pages,
                'current'   => $currentPage,
            ));
            $output .= '</div></div>';
            return $output;
        }
    }
    return null;
}
///////////
function TrimPhoneNo($phone){
    $phone = preg_replace('/\s+/', '', $phone);
    if (substr($phone, 0, 1) === "0") {
        $phone = substr($phone, 1);
    }
    return $phone;
}
//////////////
function isCCPhoneNo($no){
    $no = str_replace(' ', '', $no);
    $no = ltrim($no, "0");
    $no = trim($no);
    if (empty($no) || strpos($no, '+') !== 0) {
        return ['status' => false, 'message' => 'Phone number must start with + sign and country code.'];
    }
    
    $digits = substr($no, 1);
    if (!ctype_digit($digits)) { return ['status' => false, 'message' => 'Phone number must contain + sign and digits only.'];}

    if ($digits[0] === '0') {
        return ['status' => false, 'message' => 'Country code cannot start with 0.'];
    }

    if (strlen($no) <= 11) {
        return ['status' => false, 'message' => 'Phone number must be longer than 10 digits.'];
    }
    return ['status' => true, 'message' => 'Phone number is valid.'];
}
//////////////
function techwatt_get_counts() {
    global $wpdb;

    // ✅ Count projects (custom post type)
    $project_counts = wp_count_posts('kids_project');
    $total_published_projects = $project_counts->publish ?? 0;
    $total_projects = array_sum( (array) $project_counts ) ?? 0;

    // ✅ Count students (user role)
    $count_users = count_users();
    $total_bookings = $count_users['avail_roles']['student'] ?? 0;

    // ✅ Count total children under students
    $total_children = 0;
    $total_paid = 0; $total_cost = 0;
    $total_outstanding = 0;

    $query = new WP_User_Query([
        'role'   => 'student',
        'fields' => ['ID'],
    ]);

    foreach ($query->get_results() as $student) {
        $userdata = get_user_meta($student->ID, 'tw_userdata', true);
        if (!empty($userdata['children']) && is_array($userdata['children'])) {
            $total_children += count($userdata['children']);
        }
        
        foreach ($userdata['children'] as $child) {
            $course_cost = floatval($child['cost'] ?? 0);
            $total_cost += $course_cost;
            $paid_amount = floatval($child['paid'] ?? 0);
            $total_paid += $paid_amount;
        }        
    }
    $total_outstanding = max(0, $total_cost - $total_paid);

    // ✅ Count Testimonials (custom post type)
    $testimony_counts = wp_count_posts('testimony'); // or 'testimonial' if that's your CPT slug
    $total_published_testimonies = $testimony_counts->publish ?? 0;
    $total_testimonies = array_sum((array) $testimony_counts) ?? 0;

    // ✅ Count Bootcamp registrations
    $bootcamp_table = $wpdb->prefix . 'bootcamp_registrations';
    $total_bootcamp = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$bootcamp_table}");

    // ✅ Count Future Club registrations
    $futureclub_table = $wpdb->prefix . 'futureclub_registrations';
    $total_futureclub = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$futureclub_table}");

    return array(
        'projects'     => $total_projects,
        'published_projects'  => $total_published_projects,
        'published_testimonies' => $total_published_testimonies,
        'testimonies' => $total_testimonies,
        'bookings'     => $total_bookings,
        'children'     => $total_children,
        'bootcamp'     => $total_bootcamp,
        'futureclub'   => $total_futureclub,
        'totalcost'   => $total_cost,
        'totalpaid'   => $total_paid,
        'totaloutstanding' => $total_outstanding,
    );
}

?>