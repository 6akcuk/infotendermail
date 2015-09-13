$.fn.busy = function() {
    $('#busy').css({
        top: $(this).offset().top,
        left: $(this).offset().left,
        width: $(this).outerWidth(),
        height: $(this).outerHeight(),
        lineHeight: $(this).outerHeight() + 'px'
    }).show();
};
$(document).ajaxComplete(function() {
    $('#busy').hide();
});
