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
$ArrayPackages = ["bronze"=>["cost"=>300,"duration"=>"4","info"=>"Perfect for self-starters,No free kits, up to 30 students in class, community-based learning experience"],"silver"=>["cost"=>500,"duration"=>"4","info"=>"Free Robotic kits included, up to 20 students in class, ideal for learners who enjoy collaboration and teamwork"],"gold"=>["cost"=>700,"duration"=>"4","info"=>"Free Robotic kits included, one-on-one training sessions twice a week, Access to all previous class recording, offers complete robotics experience"]];

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

function PS_GetCurrentUrl(){
    global $wp;
    $current_url = home_url( add_query_arg( array(), $wp->request ) );
    return $current_url;
}
//////////////////////
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
function PSCurrency($price){
    $currency = get_woocommerce_currency();
    $currency_symbol = get_woocommerce_currency_symbol();
    return (!empty($price))? '('.$currency.')'.$currency_symbol.$price:'('.$currency.')'.$currency_symbol;
}
?>