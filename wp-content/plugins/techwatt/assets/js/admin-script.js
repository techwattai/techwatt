jQuery(document).ready(function($) {
    
    function toggleAdminCustomUrl() {
        if ($('#ps_iscaurl').is(':checked')) {
            $(".show_caurl").show(300);
        } else {
            $(".show_caurl").hide(300);
        }
    }
    toggleAdminCustomUrl();
    // Run whenever the checkbox changes
    $('#ps_iscaurl').on('change', function() {
        toggleAdminCustomUrl();
    });

});