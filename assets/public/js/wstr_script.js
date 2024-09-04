// scripts
jQuery(document).ready(function ($) {
    $('.ws_mega_menu').css('display', 'none');
    $('h2').on('click', function () {
        $('.ws_mega_menu').toggle();
        $(this).toggleClass('active');
    });

})