$(document).ready(function() {
    var constraints = $("#activationprizecopyconstraints-constraints"),
        state_button_delete = false,
        count_apple = $(".count_apple input");

        count_apple.mask("# ##0", {reverse: true});


    // после нажатия на кнопку Сгенерировать
    $(document).on("click", "#generate", function () {
        $(".block_progress_bar").addClass("active").show().fadeIn(3000);
        $(this).attr("disabled",true).removeClass('btn-success').addClass('btn-warning');
        $(this).children("span").removeClass('glyphicon-play').addClass('glyphicon-time');
        var progress_bar_value = 0,
            duration_id = $("#activationprizecopyconstraints-constraints").find('li[selected="selected"]').attr('duration_id'),
            count_checked_prizes_on_click = parseInt($('.copes_prize input[type="checkbox"]:checked').length),
            step_progress_bar_value = Math.round(100 / parseInt(count_checked_prizes_on_click));

    });

});
