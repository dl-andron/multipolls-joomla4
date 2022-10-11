$(function() {
    // делаем кнопку отправки неактивной
    $('.com-mulipolls__submit .submit-button').attr('disabled', true);
    // скрываем все ответы кроме первого
    $('.com-multipolls-poll .answers:not(:first)').hide();
    // при выборе ответа
    $('.com-multipolls-poll input[type="radio"], .mod-multipolls-poll input[type="radio"]').click(function(){
        if ($(this).is(':checked')){
            if($(this).closest('.answers').next().attr("class") == 'answers') {
                $(this).closest('.answers').next().show();
            } else {
                $(this).closest('.answers')
                    .siblings('.control-group')
                    .find('.submit-button')
                    .attr('disabled', false);
            }
        }
    });

    $(".com-multipolls-poll .ro-answers .own-input, .mod-multipolls-poll .ro-answers .own-input").focusin(function() {
        if($(this).closest('.answers').next().attr("class") == 'answers') {
            $(this).closest('.answers').next().show();
        } else {
            $(this).closest('.answers')
                .siblings('.control-group')
                .find('.submit-button')
                .attr('disabled', false);
        }
    });

    // если действия происходят в модуле
    var moduleOptions = Joomla.getOptions("mod-multipolls-options");
    if(moduleOptions !== undefined && moduleOptions['is_module']){
       $('form.mod-multipolls').each(function(index, el){
           $('.submit-button', el).attr('disabled', true);
           $('.answers:not(:first)', el).hide();
       });
    }
});