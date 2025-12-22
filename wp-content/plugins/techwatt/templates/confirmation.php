<?php
$status = $_GET['status'] ?? '';
$uid = $_GET['uid'] ?? '';
$childid = $_GET['childid'] ?? '';

if($userID != $uid){ wp_die("Unauthorized access."); }
//$tw_userdata = get_user_meta($uid, 'tw_userdata',true) ?? [];
//$children = $tw_userdata['children'] ?? []; 

$child = $children[$childid] ?? null;
if (!$child) { wp_die("Child not found."); }
$child_id = $child['id'] ?? '';
$child_name = $child['name'] ?? 'Your child';
$child_email = $child['email'] ?? '';
?>
<div class="account-dashboard" style="position:relative;">
<div class="trialpopup">
<?php
if($status == 'paysuccess'):
    echo '<h2>Payment Successful!</h2><p>Congratulation '.$userName.', Your course payment has been successfully processed. Child Name: '.$child_name.', Child Course ID: #'.$child_id.'. You can now access the full course content.<br>Thank you for your patronage!</p>';
elseif($status == 'payfail'):
    echo '<h2>Payment Failed</h2><p>Unfortunately, the payment for '.$child_name.'\'s course was not successful. Please try again or contact support for assistance.</p>';
else:
    echo '<h2>Confirmation Status Unknown</h2><p>The payment status could not be determined. Please contact support for assistance.</p>';
endif;
?>
<a href="<?php echo esc_url(twUrl('PS_UDashboard')); ?>" class="btn btn-primary">Continue</a>
</div>
</div>