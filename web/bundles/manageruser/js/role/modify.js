$(document).ready(function() {
    //清除form
    function cleanForm() {
        $('#name').val("");
        $("#roleId").attr('value', 0);
    }

    $(document).delegate(".js-checkbox", "click", function(){
        var parent = $(this).closest("tr");
        var flag = $(this).is(":checked");
        $("option", parent).each(function(){
            this.selected = flag;
        });
    });

    $(".js-select-mul").click(function(){
        var inputThis = $(this).closest("tr").find(".js-checkbox");
        if($(this).val()){
            inputThis[0].checked = true;
            inputThis.closest("span").addClass("checked");
        } else {
            inputThis[0].checked = false;
            inputThis.closest("span").removeClass("checked");
        }
    })

    //创建或者修改应用
    $('#submitForm').submit(function() {
        //获取被选项
        var route = [];
        $(".js-select-mul").each(function(){
            var value = $(this).val();
            if(value){
                route = route.concat(value);
            }
        });
        $(".js-checkbox").each(function(){
            var value = $(this).val();
            if(value && $(this).is(":checked")){
                route = route.concat(value);
            }
        });

        var id   = $('#entity_id').val();
        var name = $('#name').val();
        if (!name) {
            jAlert("(角色名称)不能为空!", "提示");
            return false;
        }
        var params     = {
            "id"         : id,
            "name"       : name,
            "route"      : route,
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
