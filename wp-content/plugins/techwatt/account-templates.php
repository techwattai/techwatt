<span id="msgbox" style="position:absolute;top:11%;right:20px;z-index:10;background:#f5f5f5;padding:5px 10px;border-radius:5px;display:none;"></span>
<?php
// Load custom profile page when visiting /portal/?profile
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
}else if(isset($_GET['add-project'])){
    include plugin_dir_path(__FILE__) . 'templates/kid-addproject.php';
}else if(isset($_GET['edit-project'])){
    include plugin_dir_path(__FILE__) . 'templates/kid-editproject.php';
}else if(isset($_GET['testimonies'])){
    include plugin_dir_path(__FILE__) . 'templates/testimonies.php';
}else if(isset($_GET['add-testimony'])){
    include plugin_dir_path(__FILE__) . 'templates/testimony-add.php';
}else{
    include plugin_dir_path(__FILE__) . 'templates/dashboard.php';
}
?>