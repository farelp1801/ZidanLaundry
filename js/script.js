$(document).ready(function(){
    $('.tab-link').click(function(){
        var tab_id = $(this).attr('data-tab');

        $('.tab-link').removeClass('current');
        $('.tab-content').removeClass('active');

        $(this).addClass('current');
        $("#" + tab_id).addClass('active');
    });

    // Set default tab
    $('.tab-link[data-tab="dashboard"]').addClass('current');
    $('#dashboard').addClass('active');
});
