$(document).ready(function() {
    //清除form
    function cleanForm() {
        $('#name').val("");
        $('#route').val("");
        $('#remark').val("");
        $("#firstMenu").val(0);
        $("#secondMenu").val(0);
        $("#uniform-firstMenu").find('span').html("请选择");
        $("#uniform-secondMenu").find('span').html("请选择");
    }

    //创建或者修改应用
    $('#submitForm').submit(function() {
        var id   = $('#entity_id').val();
        var name = $('#name').val();
        if (!name) {
            jAlert("(名称)不能为空!", "提示");
            return false;
        }

        var route      = $('#route').val();
        var firstMenu  = $('#firstMenu').val();
        var secondMenu = $('#secondMenu').val();
        var remark     = $('#remark').val();
        var params     = {
            "id"         : id,
            "name"       : name,
            "route"      : route,
            "firstMenu"  : firstMenu,
            "secondMenu" : secondMenu,
            "remark"     : remark,
            "confirm"    : 1
        };

        var url = GLOBAL_CONF['action_add'];

        $.ajax({
            "url": url,
            "type": "post",
            "data" : params,
            "dataType" : "json",
            "error" : function (jqXHR, textStatus, errorThrown) {
                var errMsg = errorThrown == 'Forbidden' ? "亲，没权限呢!" : "亲，服务器忙!"; jAlert(errMsg, "提示");
            },
            "success" : function (data) {
                if (data['code'] != 0) {
                    jAlert("error: " + data['msg']);
                } else {
                    jAlert("提交成功!", "提示", function() {
                        if (id) {
                            window.close();
                        } else {
                            cleanForm();
                        }
                    });
                }
            }
        });
        return false;
    });
});
