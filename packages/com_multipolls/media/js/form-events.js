$(function() {
    // предотвращает отправку формы несколько раз
    $(document).on('submit', '.poll', function () {
        $(".submit-button", this).attr("disabled", true);
    });

    // при выборе своего варианта выделить чекбокс
    $(".cbo-answers .own-input").focusin(function() {
        $(this).prev('.own-checkbox').prop("checked", true)
    });

    // при выборе своего варианта выделить радио-кнопку
    $(".ro-answers .own-input").focusin(function() {
        $( this ).prev('.own-radio').prop("checked", true)
    });

    // заголовки-спойлеры
    var spTitles = $('.slider-title');
    spTitles.siblings('.poll-body').hide();
    spTitles.on('click', function(){
        $(this).siblings('.poll-body').slideToggle(300);
    })
});