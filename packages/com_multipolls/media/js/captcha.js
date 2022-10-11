$(document).on('click', '.refresh-captcha', function () {
    var el = $(this);
    $.ajax({
        type: 'GET',
        url: window.location.origin + '/index.php?option=com_multipolls&task=captcha.setCode&' + Math.random(),
        success: function (response) {
            el.siblings('.captcha-pic').attr('src', window.location.origin +
                '/index.php?option=com_multipolls&task=captcha.render&code=' + response);
            el.siblings('.real-captcha').val(response);
        }
    });
    return false;
});