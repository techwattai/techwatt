<?php
//$tw_userdata = get_user_meta($userID, 'tw_userdata',true) ?? []; 
//$children = $tw_userdata['children'] ?? []; 

$noofKids = count($children);
$projectID = $_GET["projid"];
?>

<h2 class="ps-mb-5 ps-hdflex">
    <span>Edit Project</span>
    <span><a href="<?php _e(twUrl("PS_KidsProjects"),"tw"); ?>" class="btn btn-xs btn-light"><i class="bi bi-arrow-left-short"></i> Back to Projects</a></span></h2>

<p>Edit your project below. Asteriked fields are required. Click here to view all <a href="<?php _e(twUrl("PS_KidsProjects"),"tw"); ?>">projects</a>.</p>

<div class="ps-w-95">
    <div class="d-flex-row">
        <?php echo do_shortcode('[kids_project_form isadmin="0" id="'.$projectID.'"]'); ?>
    </div>
</div>