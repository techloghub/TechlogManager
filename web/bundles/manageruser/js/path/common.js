$(document).ready(function() {

    var showProgress = function() {
        $('#loaders').show();
    }
    var hideProgress = function() {
        $('#loaders').hide();
    }

    hideProgress();

    $('#firstMenu').change(function() {
        var firstMenu = $.trim($('#firstMenu').val());
        var params    = {
            "firstMenu" : firstMenu
        };

        var url    = GLOBAL_CONF['action_menu'];
        var option = '<option value="0">请选择</option>';

        if (firstMenu == 0) {
            $("#uniform-secondMenu").find('span').html("请选择"); //框架的Bug
            $("#secondMenu").html(option);
            $("#secondMenu").attr('value', 0);
            return true;
        }

        showProgress('loaders');

        $.ajax({
            "url": url,
            "type": "post",
            "data" : params,
            "dataType" : "html",
            "error" : function (jqXHR, textStatus, errorThrown) {
                hideProgress();
                var errMsg = errorThrown == 'Forbidden' ? "亲，没权限呢!" : "亲，服务器忙!"; jAlert(errMsg, "提示");
            },
            "success" : function (data) {
                var route_option = '';
                data = eval("("+data+")");
                if (data.result != '') {
                    $.each(data.result, function(idx, item) {
                        if (item.firstMenu == firstMenu && item.secondMenu == 0) {
                            option += '<option value="' + item.id + '">' + item.name + '</option>';
                        }
                    });
                } else {
                    $("#uniform-secondMenu").find('span').html("请选择"); //框架的Bug
                }
                hideProgress();
                $("#secondMenu").html(option);
                $("#route").html(route_option);
            }
        });

        return false;
    });
});
