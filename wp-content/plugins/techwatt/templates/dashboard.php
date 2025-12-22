<?php
global $ArrayCourses,$ArrayPackages;

//$tw_userdata = get_user_meta($userID, 'tw_userdata',true) ?? []; 
//$children = $tw_userdata['children'] ?? []; 

$noofKids = count($children);
$totalItems = ps_Total_Ordered_Products($userID);
$RegCourses = '';
$RegCourseCount = 0;

$totalReferrals = PS_MyReferral_Count($userID);

foreach($children as $child){ 
    $RegCourses .= '<div class="bi bi-arrow-right-squarex" style="margin:2px 0 0 0;">&bull; '.$ArrayCourses[$child['course']].'  -  <b>Package:</b> '.strtoupper($child['package']).' ('.$child['duration'].' months course)</div>';
    $RegCourseCount += 1;
}
?>
<h2><?php echo $greetings; ?></h2>
<!--<p>Below is the stats.  👋</p>-->
<div class="ps-w-100">
<div class="d-flex-row">
    <a href="<?php echo twUrl("PS_BookingOrders"); ?>" class="flex-1 dashbox dashcolor1"><h3><?php echo $noofKids; ?></h3>No of Kids</a>
    <a href="<?php echo twUrl("PS_BookingOrders"); ?>" class="flex-1 dashbox dashcolor3"><h3><span class="regcourses"><?php echo $RegCourseCount ?? 'N/A'; ?></span></h3>Registered Course</a>
    <a href="<?php echo twUrl("PS_KidsProjects"); ?>" class="flex-1 dashbox dashcolor2"><h3><?php echo do_shortcode('[kids_projects_count]'); ?></h3>Kids' Project</a>
    <a href="<?php echo twUrl("PS_MyReferrals"); ?>" class="flex-1 dashbox dashcolor3"><h3><?php echo $totalReferrals ?? '0'; ?></h3>My Referrals</a>
</div>

<div class="d-flex-only" style="padding-top:35px;gap:20px">
    <div class="alert alert-secondary" style="flex:1;line-height:normal;color:#333;background:rgba(23, 146, 222, 0.05);">
        <b>Registered Courses:</b> <?php echo $RegCourses; ?>  
        <a href="<?php _e(twUrl("PS_BookingOrders"),"tw"); ?>" class="btn btn-xs btn-primary" style="margin-top:10px;display:inline-block;"><i class="bi bi-arrow-right-short"></i> View All Courses</a>
        <a href="<?php _e(twUrl("PS_AddCourse"),"tw"); ?>" class="btn btn-xs btn-success" style="margin-top:10px;display:inline-block;"><i class="bi bi-plus-lg"></i> Add New Course</a>      
    </div>
    <div class="alert alert-secondary" style="flex:1;line-height:normal;color:#333;background:rgba(23, 146, 222, 0.05);">
        <div style="margin:0 0 5px 0;"><b>Referral Link:</b> <a href="<?php _e(twUrl("PS_Home").'?ref='.$userID,'tw'); ?>" target="_Blank"><?php _e(twUrl("PS_Home").'?ref='.$userID,'tw'); ?></a></div>
        <div><b>What's Next </b><i class="bi bi-arrow-right"></i> <span>
            <?php if($TrialExist): ?><a href="<?php echo twUrl("PS_BookingOrders"); ?>">Pay</a> for your trial class :: <?php endif; ?>
            <a href="<?php echo twUrl("PS_UProfile"); ?>">Update</a> your profile :: <a href="<?php echo twUrl("PS_KidsProjects"); ?>">Add/View</a> projects<span></div>
    </div>
    
</div>

<!--<div class="" style="padding-top:25px;">
    <h3 class="morehd" style="margin-bottom:15px;">Top 10 Most Viewed Products</h3>
    <div class="d-flex-row psmostviewed">
        <?php //echo do_shortcode('[ps_mostviewed_products]'); ?>
    </div>
</div>-->

</div>