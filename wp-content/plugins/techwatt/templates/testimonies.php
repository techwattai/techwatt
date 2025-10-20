<h2 class="ps-mb-5 ps-hdflex"><span>Testimonies</span> <span><a href="javascript:;" class="showtmForm btn btn-xs btn-success"><i class="bi bi-plus"></i> Share Testimony</a></span></h2>

<div class="ps-w-100">
<div id="tmcontainer" style="display:none;"><?php echo do_shortcode('[testimony_form]'); ?></div>    
<div class="ps-mt-10">
        <?php echo do_shortcode('[testimonies_view view="grid" posts_per_page="20"]'); ?>
    </div>
</div>
<script>
jQuery(function($){
    $("#tmcontainer").hide(200);
    $(".showtmForm, .canceltmForm").on('click',function(){
        $("#tmcontainer").slideToggle();
    });
});
</script>