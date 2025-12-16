<?php
//$tw_userdata = get_user_meta($userID, 'tw_userdata',true) ?? []; 
//$children = $tw_userdata['children'] ?? []; 
$noofKids = count($children);
?>

<h2 class="ps-mb-5 ps-hdflex">
    <span>Add New Course</span>
    <span><a href="<?php _e(twUrl("PS_BookingOrders"),"tw"); ?>" class="btn btn-xs btn-light"><i class="bi bi-arrow-left-short"></i> Back to Course</a></span></h2>

<p>Use the form below to add new course. Asteriked fields are required. Click here to view all <a href="<?php _e(twUrl("PS_BookingOrders"),"tw"); ?>">registered courses</a>.</p>

<div class="ps-w-95">
    <div class="d-flex-row">
        <?php echo do_shortcode('[ps_booktrial userid="'.$userID.'"]');  ?>
    </div>
</div>