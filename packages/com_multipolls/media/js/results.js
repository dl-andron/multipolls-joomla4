$(function() {
    $(document).on('click', '.result-button', function () {
        var pollForm = $(this).closest('form');
        var id  = pollForm.children('input[name=id]').val();
        $.ajax({
            url: window.location.origin + '/index.php?option=com_multipolls&task=poll.getResults',
            data: { id: id },
            success: function(response) {
                pollForm.hide();
                pollForm.siblings('.results').html(response).append();
            },
            error: function() {
                console.log('error');
            },
        });
    });

    $(document).on('click','.back-to-poll',function(){
        $(this).parents('.results').siblings('form').show();
        $(this).parents('.results').empty();
    });
});