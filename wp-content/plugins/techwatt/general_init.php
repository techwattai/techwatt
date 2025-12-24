<?php
//$CURRENT_USER
//add_filter('show_admin_bar', '__return_false'); // Remove admin bar for all users

// Track product views
function tw_track_product_views() {
    if (is_singular('product')) {
        global $post;
        $views = (int) get_post_meta($post->ID, 'views_count', true);
        $views++;
        update_post_meta($post->ID, 'views_count', $views);
    }
}
add_action('template_redirect', 'tw_track_product_views');

//////////// DATABASE CREATION ////////////
function activateDB() {
    global $wpdb;
    $charset = $wpdb->get_charset_collate(); 
    $payments = "{$wpdb->prefix}course_payments";

    $sql1 = "CREATE TABLE IF NOT EXISTS $payments (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            userid BIGINT UNSIGNED NOT NULL,
            childid VARCHAR(45) NOT NULL,
            parent_name VARCHAR(45) NULL,
            course VARCHAR(50) NULL,
            amount DECIMAL(10,2) NOT NULL,
            currency VARCHAR(10) NOT NULL DEFAULT 'GBP',
            payment_status VARCHAR(20) NOT NULL DEFAULT 'pending',
            refno VARCHAR(255) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id), KEY refno (refno)
        ) $charset;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql1);
}

register_activation_hook( PS_PLUGIN_FILE, 'activateDB' );

////////// Team Profile Shortcode ////////// [team_profile]
add_shortcode('team_profile', function($atts) {
    ob_start();  //include PS_PLUGIN_PATH.'templates/team-profile.php';
?>
<div id="content-felix" class="teamprofile" style="display:none;"><h3>Felix Aduloju</h3><h5>Co-Founder & CTO</h5><p>Felix  shapes the company's vision at the intersection of AI, robotics, and intelligent automation. He holds an MSc in Artificial Intelligence (Distinction) from the University of Bradford ,UK and is professionally certified across security, cloud, and technology strategy.</p><p>With over 15 years of global experience, Felix has delivered complex, AI-driven solutions for critical infrastructure, including airports, rail networks, data centres, and landmark projects such as Dubai Expo 2020. Renowned for translating advanced research into real-world systems, he leads TechWatt's technical strategy-driving scalable innovation across AI automation, smart infrastructure, and IoT-enabled robotics.</p><p>His mission is clear: build future-ready technology, empower engineers, and position the UK and Africa as a global force in AI and robotics innovation.</p>
</div>

<div id="content-sam" class="teamprofile" style="display:none;"><h3>Nanor Samuel Felix Tetteh</h3><h5>Co-Founder & Global CEO</h5><p>Sam  is a visionary AI engineer and technology leader driving TechWatt AI’s mission to build intelligent systems that create real-world impact. With a strong engineering foundation in Electrical & Electronics Technology, he bridges hardware, software, and artificial intelligence to deliver scalable, high-performance solutions.</p><p>An accomplished AI and Machine Learning Engineer, Felix specializes in robotics, automation, and data-driven systems that enhance efficiency across industries. He also serves as Lead AI & ML Engineer at QOLMT (Canada), where he develops intelligent platforms that optimize business operations.</p><p>At TechWatt AI, he leads both AI Solutions, delivering custom automation and analytics, and the AI Academy, providing world-class training in Python, Machine Learning, Robotics, and Computer Vision - empowering a new generation of innovators, with a strong focus on Africa.</p>
</div>

<div id="content-johnson" class="teamprofile" style="display:none;"><h3>Olumide Johnson Ikumapayi</h3><h5>Co-Founder and CEO, UK</h5><p>Olumide leads  strategy, business growth, and data-driven innovation. With over 20 years’ experience spanning management consulting, financial analytics, and enterprise risk management, he brings a disciplined financial lens to advanced technology solutions.</p><p>Olumide holds an MSc in Financial Technology (Merit) from the University of Bradford and advanced certifications in data science, machine learning, and analytics. He is a Fellow of ICAN and AERMP, and a member of AAAI and ACFE.</p><p>At TechWatt, he aligns financial strategy with AI-powered innovation, driving sustainable growth, cross-functional execution, and global competitiveness. Beyond the company, he contributes to academia and innovation ecosystems as a reviewer and judge at leading U.S. science and research programs, reflecting his commitment to mentorship, education, and future-ready talent development.</p>
</div>

<div id="content-femi" class="teamprofile" style="display:none;"><h3>Oluwafemi Sangolade</h3><h5>Innovation Director</h5><p>Femi leads the design and delivery of cutting-edge digital learning solutions and technology-driven training strategies. With over seven years of cross-industry experience spanning healthcare, IT consulting, professional services, and insurance, he brings a rare blend of technical depth and creative vision to educational innovation.</p><p>Holding a Bachelor’s degree in Computer Engineering, Oluwafemi specialises in eLearning design, LMS/LXP implementation, and scalable training systems that integrate artificial intelligence, data analytics, and modern design principles. His multidisciplinary background in media production, graphics design, and instructional technology enables him to create engaging, high-impact learning experiences for global audiences.</p><p>At TechWatt, he plays a pivotal role in advancing accessible, future-ready learning—empowering organisations and individuals to thrive in the digital and AI-driven era.</p>
</div>

<div id="content-maxwell" class="teamprofile" style="display:none;"><h3>Jeremiah Maxwell</h3><h5>Chief Operating Officer (UK)</h5><p>Jeremiah Maxwell is the Chief Operating Officer at TechWatt Global Technology Ltd. (UK), leading operations, technology strategy, and enterprise-wide digital transformation. With over 15 years of experience across information technology, media technology, fintech, and product management, he brings a rare balance of technical depth, operational rigor, and entrepreneurial vision.</p><p>Holding both BSc and MSc degrees in Information Technology, Jeremiah has led large-scale technology deployments, optimized digital platforms, and delivered high-impact solutions spanning live-streaming architecture, AV integration, IT infrastructure, and digital content strategy. His career also includes building and managing a successful fashion business in Nigeria, sharpening his commercial and leadership acumen.</p><p>At TechWatt, he translates vision into execution—aligning AI solutions, corporate training, and strategic partnerships with global growth goals—driving innovation, efficiency, and world-class user experience across all offerings.</p>
</div>

<?php
    return ob_get_clean();
});