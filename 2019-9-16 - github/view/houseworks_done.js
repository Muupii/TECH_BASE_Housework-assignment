$(function() {
    $("[name='flag']").click(function(){
        let num = $("[name='flag']").index(this);
        if(num == 0){
            $("#housework").attr('placeholder', '例：ポストに入っていた不要なチラシの処分');
            $("#housework_time").css('display', 'none');
        } else {
            $("#housework").attr('placeholder', '例：洗濯物を畳んだ');
            $("#housework_time").css('display', 'block');
        }
    });

    $("#housework_noname_list_trigger").click(function(){
        if ($("#housework_noname_list_ul").css('display') == 'none') {
            $("#housework_noname_list_ul").css('display', 'block');
        } else {
            $("#housework_noname_list_ul").css('display', 'none');
        }
    })

});