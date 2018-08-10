$(document).ready(function() {
    //清除form
    function cleanForm() {
        $('#username').val("");
        $("#roleId").val(0);
        $("#uniform-roleId").find('span').html("请选择");
    }

    //创建或者修改应用
    $('#submitForm').submit(function() {
        var id   = $('#entity_id').val();
        var username = $('#username').val();
        if (!username) {
            jAlert("(用户名称)不能为空!", "提示");
            return false;
        }
        var roleId = $('#roleId').val();
        if (roleId == 0) {
            jAlert("请选择(角色名称)!", "提示");
            return false;
        }
        var params     = {
            "id"       : id,
            "username" : username,
            "roleId"   : roleId,
            "confirm"  : 1
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
