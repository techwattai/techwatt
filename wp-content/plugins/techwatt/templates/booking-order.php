<h2 style="margin:0 0 5px 0;">My Booking</h2>
<p>Hey <?php echo wp_get_current_user()->display_name; ?>, here's a list of classes booked on our platform for you or your kids.<br><b style="color:red;">Important Status: </b> <b>Trial</b> means trial class, <b>"Pending"</b> - Make payment, and <b>"Paid"</b> - Payment made.</p>
<?php
global $CountryCodes,$ArrayCourses;
$userID = $user->ID;  $ck=0;
////////// GET USER META ////////////////boolean means return single value (true) or array (false)
$children = get_user_meta($userID, 'children',true); 
   
    foreach($children as $childKey=>$child){ //K=>$childV
        $child_id = $child["id"] ?? $childKey;
        $child_name = $child["name"] ?? 'N/A';
        $child_age = $child["age"] ?? 'N/A';
        $child_course = $ArrayCourses[strtolower($child["course"])] ?? 'N/A';
        $child_class = $child["class"] ?? 'N/A';
        $child_regdate = (!empty($child["regdate"])) ? date('Y-m-d h:i A',$child["regdate"]) : 'N/A';
        $child_cost = $child["cost"] ?? 0;

        $child_paymentstatus = $child["paymentstatus"] ?? '';
        if(empty($child_paymentstatus)){ $child_paymentstatus = '<trial>Trial Class</trial>'; }
        elseif($child_paymentstatus === 'pending'){ $child_paymentstatus = '<pending>'.ucwords($child_paymentstatus).' Payment</pending>'; }
        elseif($child_paymentstatus === 'paid'){ $child_paymentstatus = '<success>Payment '.ucwords($child_paymentstatus).'</success>'; }

        $child_img = (!empty($child["img"]))? $child["img"]:PS_NoImage;
        $ck++;
?>
<div class="boxrow">

    <div class="itemrow" style="border:#eee solid 1px;">
        <div>
            <p class="ps-mb-2"><img src="<?php echo $child_img; ?>" style="height:80px !important;width:auto;border-radius:5px;margin:-10% 0 5% 0;"></p>
            <p class="ps-mb-1"><strong>ID #: </strong> <?php echo strtoupper($child_id); ?></p>
            <p class="ps-mb-1"><strong>Child's Name: </strong> <?php echo $child_name; ?></p>
            <p class="ps-mb-1"><strong>Child's Age: </strong> <?php echo $child_age; ?> years</p>
            <p class="ps-mb-1"><strong>Course: </strong> <?php echo (!empty($child_course))?$child_course:'N/A'; ?></p>
            <p class="ps-mb-1"><strong>Class: </strong> <?php echo (!empty($child_class))?$child_class:'N/A'; ?></p>
            <p class="ps-mb-1"><strong>Cost: </strong> <?php echo ((float)$child_cost > 0)?PSCurrency($child_cost):PSCurrency('0.00'); ?></p>
            <p class="ps-mb-1"><strong>Status: </strong> <?php echo $child_paymentstatus; ?></p>
            <p class="ps-mb-1"><strong>Register Date: </strong> <?php echo $child_regdate; ?></p>
        </div>
    </div>
    
    
</div>
<?php
}
?>