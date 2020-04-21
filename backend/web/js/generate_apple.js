$(document).ready(function() {
    var constraints = $("#activationprizecopyconstraints-constraints"),
        count_apple_input = $(".count_apple input"),
        start_generate = false,
        progress_bar_value,
        genereted_count;

    count_apple_input.mask("# ##0", {reverse: true});

    // check view event delete all items entity
    if (parseInt($("#count_" + getEntity()).attr('count')) > 0){
        $("#delete_all_apples").removeAttr('disabled');
    } else {
        $("#delete_all_apples").attr('disabled');
    }

    // function return get selected entity name
    function getEntity() {
        return $(".nav_entities > li.active").attr('entity')
    }

    // send batches for generation until the total runs out
    // and mapping the generation process to pages
    function send_batch_generate(count_apple,cnt_batch_item,batch_id) {
        var tmp_count,
            limit_cnt_in_batch = parseInt(generate_params['cnt_in_batch']);

        if (count_apple > limit_cnt_in_batch){
            tmp_count = limit_cnt_in_batch;
            count_apple -= limit_cnt_in_batch
        } else {
            tmp_count = count_apple;
        }
        $.post({
            url: ajax_request_url['start_generate'],
            type: 'POST',
            data: {
                entity: getEntity(),
                count: tmp_count,
                batch_id: batch_id
            },
            success: function(response) {
                if (response.result.status == generate_params['status_generated']){
                    genereted_count += parseInt(response.result.inserted);
                    progress_bar_value += cnt_batch_item;
                    progress_bar_value = progress_bar_value > 100 || progress_bar_value >= 93 ? 100 : progress_bar_value;
                    var text_progress_bar = 'Сгенерировано (' + genereted_count + ')   ' + progress_bar_value + "%";
                    text_progress_bar = progress_bar_value > 20 ? text_progress_bar : progress_bar_value + "%";
                    $(".progress-bar").text(text_progress_bar)
                        .attr("aria-valuenow",progress_bar_value).css("width",progress_bar_value + "%");
                }
            },
            error: function (response) {
                throw new Error(response);
            }
        }).done(function() {
            if ((progress_bar_value >= 93 && progress_bar_value <= 100) || progress_bar_value > 100) {
                $(".progress-bar").text("100%").attr("aria-valuenow", "100").css("width", "100%").text('Генерация завершена');
                $(".block_progress_bar").removeClass("active");
                $("#generate").children(".glyphicon-play").show().siblings(".span_generate_loader").hide();

                if (progress_bar_value == 100){
                    setTimeout(function () {
                        location.reload();
                    }, 2800);
                }
            } else {
                send_batch_generate(count_apple, cnt_batch_item, batch_id);
            }
        })
    }

    // event delete all selected entity
    $(document).on("click", "#delete_all_apples", function () {
        $(this).children(".glyphicon-trash").hide();
        $(this).children(".span_delete_loader").show();
        $.post({
            url: ajax_request_url['delete_entity_data'],
            type: 'POST',
            data: {
                entity: getEntity()
            }
        }).done(function() {
            location.reload();
        });
    });

    // event start generate after click button
    $(document).on("click", "#generate", function () {
        if (start_generate === false){
            $(this).children(".glyphicon-play").hide();
            $(this).children(".span_generate_loader").show();
            start_generate = true;
            genereted_count = 0;
            $(".block_progress_bar").addClass("active").show().fadeIn(3000);
            $(this).attr("disabled",true);
            $("#delete_all_apples").attr("disabled",true);
            count_apple_input.attr("disabled",true);
            progress_bar_value = 1;

            var cnt_batch_item,
                last_batch_id = $("#last_batch_id_" + getEntity()).attr('last_batch_id'),
                count_apple = count_apple_input.val().replace(' ',''),
                cnt_batches = Math.floor(count_apple / parseInt(generate_params['cnt_in_batch']));

            cnt_batches = cnt_batches == 0 ? 1 : cnt_batches;
            cnt_batch_item = Math.floor(100 / cnt_batches);
            last_batch_id = last_batch_id > 0 ? last_batch_id : 1;

            send_batch_generate(count_apple,cnt_batch_item,last_batch_id);
        }
    });

    // check input value, It's count input generate entity validator
    $(document).on(".count_apple input",function(ev){
        var len_count_input = $(".count_apple input");
        var val = $(ev.target).val().trim().replace(' ','');
        console.log(parseInt(val.substr(0,1)));
        if (val != '' && parseInt(val.substr(0,1)) != 0 && parseInt(val) > 0){
            $("#generate").removeAttr('disabled');
            $(this).find(".label-danger").text('').css("display", "none");
            len_count_input.removeClass("has-error");
        } else {
            $("#generate").attr('disabled',true);
            len_count_input.addClass("has-error");
            $(this).find(".label-danger").text('Введите корректные данные').css("display", "block");
        }
    });

    // event eat entity
    $(document).on("click", '[action_state="' + action_params['action_eat'] + '"]', function () {
        var parent_elem = $(this).parents('ul'),
            this_state = parent_elem.attr('state'),
            id = parent_elem.attr('entity_id');

        if (this_state == state_params['state_down']){
            $.post({
                url: ajax_request_url['eat_entity'],
                type: 'POST',
                data: {
                    entity: getEntity(),
                    id: id
                },
                success: function (response) {
                    var size = response.result.size;
                    size = size == 0 ? size : Math.round(size * 100);
                    parent_elem.parent("div").siblings(".item_info").find("b:first span").text(size + "%");
                    if (size < 1){
                        parent_elem.parents(".item_apple").addClass("item_apple_action");
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    }
                },
                error: function (response) {
                    alert('Ошибка сервера');
                    throw new Error(response);
                }
            });
        } else {
            alert('Можно съесть только когда оно лежит на земле или не испорчено!');
        }
    });

    // event drop entity
    $(document).on("click", '[action_state="' + action_params['action_drop'] + '"]', function () {
        var parent_elem = $(this).parents('ul'),
            this_state = parent_elem.attr('state'),
            id = parent_elem.attr('entity_id');

        if (this_state == state_params['state_on_tree']) {
            $.post({
                url: ajax_request_url['drop_entity'],
                type: 'POST',
                data: {
                    entity: getEntity(),
                    id: id
                },
                success: function (response) {
                    parent_elem.parent("div").siblings(".item_info").find("b:nth-child(2) span").text(response.result.state_title);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                },
                error: function (response) {
                    alert('Ошибка сервера');
                    throw new Error(response);
                }
            });
        } else {
            alert('Можно сбросить только когда оно на дереве!');
        }
    });
});
