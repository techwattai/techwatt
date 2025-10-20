<h2 class="ps-mb-5 ps-hdflex">
    <span>Kids Projects</span>
    <span><a href="<?php _e(PS_AddProject,"tw"); ?>" class="btn btn-xs btn-success"><i class="bi bi-plus"></i> Share New Project</a></span></h2>

<p>Here you can view all your (or child's) projects.. Click share button above to share a new project!</p>
<div class="ps-w-100">
    <div class="">
        <?php //echo do_shortcode('[kids_projects_grid limit="20" columns="3" user_id="'.$userID.'"]'); ?>
        <?php echo do_shortcode('[kids_projects_list user_id="'.$userID.'"]'); ?>
    </div>
</div>