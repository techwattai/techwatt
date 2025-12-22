<?php
global $CountryCodes,$ArrayCourses, $ArrayPackages;
$userID = $user->ID;  $ck=0;
////////// GET USER META //////////////// boolean means return single value (true) or array (false)
//$tw_userdata = get_user_meta($userID, 'tw_userdata',true) ?? []; 
//$children = $tw_userdata['children'] ?? [];  
$MyCountryCode = $tw_userdata['countrycode'] ?? ''; 
$MyPhone = $tw_userdata['phone'] ?? ''; 
?>

<div class="innerwrap">
    <h2 class="ps-mb-1">My Profile</h2>
<p>Use the boxes below to update your profile. Asteriked fields are required.</p>
    <form id="psFrm" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
        <?php wp_nonce_field('ps_edit_profile','security'); ?>
        <input type="hidden" name="action" value="ps_editprofile">
        <input type="hidden" name="tw_userid" id="tw_userid" value="<?php echo $userID; ?>">
    <div class="d-flex-row">
        <p class="flex-1">
            <label for="tw_parentname">Parent Name</label><br>
            <input type="text" id="tw_parentname" name="tw_parentname" value="<?php echo esc_attr($user->nickname); ?>" required>
        </p>
        <p class="flex-1">
            <label for="display_name">Display Name</label><br>
            <input type="text" id="display_name" name="display_name" value="<?php echo esc_attr($user->display_name); ?>" required>
        </p>
        
    </div>
    <div class="d-flex-row">
        <p class="flex-1">
            <label for="countrycode">Country Code</label><br>
            <select name="countrycode" id="countrycode" required>
                        <?php foreach ($CountryCodes as $code => $dial): ?>
                            <option value="<?php echo esc_attr($dial); ?>" <?php echo ($MyCountryCode == $dial)?' selected':''; ?>>
                                <?php echo esc_html($code.' '.$dial); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
        </p>
        <p class="flex-1">
            <label for="tw_phone">Phone</label><br>
            <input type="text" id="tw_phone" name="tw_phone" value="<?php echo esc_attr($MyPhone); ?>">
        </p>
        <p class="flex-2">
            <label for="tw_email">Email Address</label><br>
            <input type="email" id="tw_email" name="tw_email" value="<?php echo esc_attr($user->user_email); ?>" required>
        </p>
    </div>
    <div class="subkidhd"><span>Kids' Information</span><hr></div>
    <?php
    //print_r($children);
    foreach($children as $childKey=>$child){ //K=>$childV
        $child_id = $child["id"] ?? $childKey;
        $child_name = $child["name"] ?? '';
        $child_age = $child["age"] ?? '';
        $child_course = $child["course"] ?? '';
        $child_class = $child["class"] ?? '';
        $child_regdate = $child["regdate"] ?? '';
        $child_cost = $child["cost"] ?? '';
        $child_package = $child["package"] ?? '';
        $child_duration = $child["duration"] ?? '';
        $child_paid = $child["paid"] ?? '';
        $ck++;
    ?>
    
    <div class="d-flex-row">                
        <p style="flex:1;">
            <input type="hidden" name="tw_childid[]" id="tw_childid<?php echo $ck; ?>" value="<?php echo $child_id; ?>">
                <label for="tw_childage">Child's Age <?php echo $child_age; ?><span class="red">*</span></label>
                <select name="tw_childage[]" id="tw_childage<?php echo $ck; ?>" required><option value=""></option>
                <?php for($age = AgeMin; $age<=AgeMax; $age++): ?>
                    <option value="<?php echo esc_attr($age); ?>" <?php echo (isset($child_age) &&  esc_attr($child_age) == $age)?' selected':''; ?>>Age <?php echo esc_html($age); ?></option>
                <?php endfor; ?>
                </select>
        </p> 
        <p style="flex:1;">
                <label for="tw_childname">Child's Name<span class="red">*</span></label>
                <input type="text" name="tw_childname[]" id="tw_childname<?php echo $ck; ?>" value="<?php echo $child_name; ?>" required>
        </p> 
        <p style="flex:1;">
                <label for="tw_childcourse">Selected Course<span class="red">*</span></label>
                <select name="tw_childcourse[]" id="tw_childcourse<?php echo $ck; ?>" required><option value=""></option>
                <?php foreach($ArrayCourses as $k=>$v): ?>
                    <option value="<?php echo esc_attr($k); ?>" <?php echo (isset($child_course) &&  strtolower($child_course) === strtolower($k))?' selected':''; ?>><?php echo esc_html($v); ?></option>
                <?php endforeach; ?>
                </select>
        </p>
        <p style="flex:1;">
                <label for="tw_childpackage">Selected Package<span class="red">*</span></label>
                <input type="hidden" name="tw_childpackage[]" id="tw_childpackage<?php echo $ck; ?>" value="<?php _e(strtolower($child_package),'tw'); ?>" />
                <input type="text" name="tw_childpkinfo[]" id="tw_childpkinfo<?php echo $ck; ?>" value="<?php echo esc_attr(strtoupper($child_package)).' ('.DEFAULT_CURRENCY_SYMBOL.esc_attr($child_cost).')'; ?>" readonly style="background:#f5f5f5;" readonly>
                
                <!--<select name="tw_childpackage[]" id="tw_childpackage" required><option value=""></option>
                <?php foreach($ArrayPackages as $pk=>$pv): ?>
                    <option value="<?php //echo strtolower(esc_attr($pk)); ?>" <?php //echo (strtolower($child_package) == strtolower($pk))?' selected':''; ?>><?php //echo esc_html(ucwords($pk)).' Package ('.DEFAULT_CURRENCY_SYMBOL.esc_html($pv["cost"]).' for '.esc_html($pv["duration"]).' months)'; ?></option>
                <?php endforeach; ?>
                </select>-->
        </p>                
    </div>
    <?php } ?>
        <p>
            <button type="submit" name="btneditprofile" id="btneditprofile" class="button button-primary">Save Changes</button>
        </p>
    
    </form>
</div>