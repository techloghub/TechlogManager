$(document).ready(function() {
    var showProgress = function() {
        $('#loaders_query').show();
    }
    var hideProgress = function() {
        $('#loaders_query').hide();
    }
    var datetimeConfig = {
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss',
        showSecond: true
    };
    $('#startTime').datetimepicker(datetimeConfig);
    $('#endTime').datetimepicker(datetimeConfig);

    hideProgress();

    var getParams = function() {
       var startTime  = $.trim($('#startTime').val());
       var endTime    = $.trim($('#endTime').val());
       var name       = $.trim($('#name').val());
       var operator   = $.trim($('#operator').val());
       var firstMenu  = $.trim($('#firstMenu').val());
       var secondMenu = $.trim($('#secondMenu').val());
       var params     = {
           "startTime"  : startTime,
           "endTime"    : endTime,
           "name"       : name,
           "operator"   : operator,
           "firstMenu"  : firstMenu,
           "secondMenu" : secondMenu
       };

       return params;
    }

    $('#queryform').submit(function() {
       var params = getParams();
       var url    = GLOBAL_CONF['action_query'];

       showProgress();

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
                hideProgress();
                $("#query_result").html(data);
                bindEvt(true);
            }
        });

       return false;
    });

    //查询结果(page为0表示当前页)
    var queryResult = function(start) {
        if (!start) {
            start = $('#start').val();
        }
        var params = getParams();
        params["start"] = start;
        params["limit"] = $('#limit').val();

        showProgress();

        var url = GLOBAL_CONF['action_query'];
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
                $("#query_result").html(data);
                hideProgress();
                bindEvt(true);
            }
        });
    }

    //bind分页及其他事件
    var bindEvt = function(needUniform) {
        //对bind的页面样式处理
        if (needUniform) {
            $("#query_result").find('input:checkbox, input:radio, select.uniformselect, input:file').uniform();
        }

        //删除
        $('.app_delete').click(function() {
            var id = $(this).parents("tr:eq(0)").find("td:eq(0)").text();
            var url = GLOBAL_CONF['action_delete'];
            jConfirm("是否删除?", "提示", function(r) {
                if (!r) {
                    return false;
                }
                $.ajax({
                    "url": url,
                    "type": "post",
                    "data" : {"id" : id},
                    "dataType" : "json",
                    "error" : function (jqXHR, textStatus, errorThrown) {
                        var errMsg = errorThrown == 'Forbidden' ? "亲，没权限呢!" : "亲，服务器忙!"; jAlert(errMsg, "提示");
                    },
                    "success" : function (data) {
                        if (data['code'] != 0) {
                            jAlert("error: " + data['msg'], "提示");
                        } else {
                            queryResult(0);
                        }
                    }
                });
            });

            return false;
        });

        //分页
        $('.pagination').jqPagination({
            current_page: $('#start').val(),
            max_page: $('#totalPages').val(),
            page_string: '当前页 {current_page} 共 {max_page} 页', 
            paged: function(page) {
                // do something with the page variable
                queryResult(page);
            }
        });
    };

    bindEvt(false);

})
