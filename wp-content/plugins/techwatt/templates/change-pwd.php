<?php
global $CountryCodes,$ArrayCourses;
$userID = $user->ID;
?>

<div class="innerwrap ps-w-50">
    <h2 class="ps-mb-1">Change Password</h2>
<p>Use the boxes below to reset your password. Asteriked fields are required.</p>
    <form id="psFrm1" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
        <?php wp_nonce_field('ps_chgpwd','security'); ?>
        <input type="hidden" name="action" value="ps_chgpwd">
        <input type="hidden" name="tw_userid" id="tw_userid" value="<?php echo $userID; ?>">
    <div class="d-flex-row">
        <p class="flex-1">
            <label for="tw_pwd">New Password</label><br>
            <input type="password" id="tw_pwd" name="tw_pwd" value="" required>
        </p>        
    </div>
    <div class="d-flex-row">
        <p class="flex-1">
            <label for="tw_cpwd">Confirm New Password</label><br>
            <input type="password" id="tw_cpwd" name="tw_cpwd" value="" required>
        </p>        
    </div>
      <p><button type="submit" name="btn1" id="btn1" class="button button-primary">Update Password</button></p>    
    </form>
</div>