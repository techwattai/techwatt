<h2 class="ps-mb-5 ps-hdflex">
    <span>My Booking</span>
    <span><a href="<?php _e(twUrl("PS_AddCourse"),"tw"); ?>" class="btn btn-xs btn-success"><i class="bi bi-plus-lg"></i> Add New Course</a></span>
</h2>
<p>Hey <?php echo wp_get_current_user()->display_name; ?>, here's a list of courses booked on our platform for you or your kids.<br><b style="color:red;">Course Status: </b>  <b>Trial</b> means trial course, <!--<b>Pending payment</b> - Course booked but yet to make payment/full payment, --><b>Active</b> - Fully paid course.</p>
<?php
global $CountryCodes,$ArrayCourses;
$userID = $user->ID;  $ck=0;
?>
<div style="display:flex;flex-wrap:wrap;gap:15px;">
<?php
////////// GET USER META ////////////////boolean means return single value (true) or array (false)

//$tw_userdata = get_user_meta($userID, 'tw_userdata',true) ?? []; 
//$children = $tw_userdata['children'] ?? []; 
   
    foreach($children as $childKey=>$child){ //K=>$childV
        $child_id = $child["id"] ?? $childKey;
        $child_name = $child["name"] ?? 'N/A';
        $child_age = $child["age"] ?? 'N/A';
        $child_course = $ArrayCourses[strtolower($child["course"])] ?? 'N/A';
        $child_class = $child["class"] ?? 'N/A';
        $child_package = ucwords($ArrayPackages[$child["package"]]["name"]) ?? 'N/A';
        $child_regdate = (!empty($child["regdate"])) ? date('Y-m-d h:i A',$child["regdate"]) : 'N/A';
        $child_cost = (float)$child["cost"] ?? 0;
        $child_paidamount = (float)$child["paid"] ?? 0;
        $child_balance = $child_cost - $child_paidamount;
        $btnPayNow = '<hr style="margin:10px 0 15px 0;background:#ddd;border:none;height:1px;">';

        if( $child_balance <= 0 ){
            $child_paidstatus = '<span class="badge-success">Paid</span>';  
            $btnPayNow .= '<a href="javascript:;" class="btn btn-sm" style="color:#999;background:#ddd;">Cancel</a>';   
        }else{            
            if($child_paidamount <= 0){
                $child_paidstatus = '<span class="badge-secondary">Unpaid</span>';
                
                $btnPayNow .= '<a href="javascript:;" id="btn-cancel-course" class="btn btn-sm btn-danger" data-childid="'.$child_id.'" data-uid="'.$userID.'" data-apicall="cancel-course" style="margin-right:10px;">Cancel</a>';
                
                $btnPayNow .= '<a href="javascript:;" class="btn btn-sm btn-primary" id="stripe-pay-btn" data-amount="'.$child_balance.'" data-currency="gbp" data-childid="'.$child_id.'" data-uid="'.$userID.'" data-apicall="cstripe-pay" data-name="'.$userName.'" data-email="'.$userEmail.'">Pay Now ('.PSCurrencySymbol($child_balance).')</a>';

            }elseif($child_balance > 0 && $child_paidamount > 0){
                $child_paidstatus = '<span class="badge-warning">Partially Paid</span>';
                $btnPayNow .= '<a href="javascript:;" class="btn btn-sm btn-primary" id="stripe-pay-btn" data-amount="'.$child_balance.'" data-currency="gbp" data-childid="'.$child_id.'" data-uid="'.$userID.'" data-apicall="cstripe-pay" data-name="'.$userName.'" data-email="'.$userEmail.'">Pay Balance Now ('.PSCurrencySymbol($child_balance).')</a>';

            }
        }

        $child_paymentstatus = $child["paymentstatus"] ?? '';
        if(empty($child_paymentstatus)){ $child_paymentstatus = '<trial>Trial</trial>'; }
        elseif($child_paymentstatus === 'pending'){ $child_paymentstatus = '<pending>Pending Payment</pending>'; }
        elseif($child_paymentstatus === 'paid'){ $child_paymentstatus = '<success>Active</success>'; }

        $child_img = (!empty($child["img"]))? $child["img"]:PS_NoImage;
        $ck++;
?>
<div style="flex:1 1 300px;max-width:300px;min-height:310px;border:#eee solid 1px;padding:15px;border-radius:5px;box-shadow:0 2px 5px rgba(0,0,0,0.1);margin-bottom:15px;position:relative;">
            <p class="ps-mb-2"><img src="<?php echo $child_img; ?>" style="position:absolute;right:10px;top:-10px;height:50px !important;width:50px;border-radius:5px;"></p>
            <p class="ps-mb-1"><strong>ID #: </strong> <?php echo strtoupper($child_id); ?></p>
            <p class="ps-mb-1"><strong>Child's Name: </strong> <?php echo $child_name; ?></p>
            <p class="ps-mb-1"><strong>Child's Age: </strong> <?php echo $child_age; ?> years</p>
            <p class="ps-mb-1"><strong>Course Name: </strong> <?php echo (!empty($child_course))?$child_course:'N/A'; ?></p>
            <p class="ps-mb-1"><strong>Course Package: </strong> <?php echo (!empty($child_package))?$child_package:'N/A'; ?></p>
            <p class="ps-mb-1"><strong>Cost: </strong> <?php echo ((float)$child_cost > 0)?PSCurrencySymbol($child_cost):PSCurrencySymbol('0.00'); ?></p>
            <p class="ps-mb-1"><strong>Course Status: </strong> <?php echo $child_paymentstatus; ?></p>
            <p class="ps-mb-1"><strong>Payment Status: </strong> <?php echo $child_paidstatus; ?></p>
            <p class="ps-mb-1"><strong>Register Date: </strong> <?php echo $child_regdate; ?></p>            
            <?php echo $btnPayNow; ?>
</div>
<?php
}
?>
</div>