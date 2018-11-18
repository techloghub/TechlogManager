$(document).ready(function() {
    var datetimeConfig = {
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss',
        showSecond: true
    };
    if ('time_keys' in GLOBAL_CONF) {
        for (var index in GLOBAL_CONF['time_keys']) {
            var key = GLOBAL_CONF['time_keys'][index];
            $('#start_' + key).datetimepicker(datetimeConfig);
            $('#end_' + key).datetimepicker(datetimeConfig);
        }
    } else {
        $('#start_insert_time').datetimepicker(datetimeConfig);
        $('#end_insert_time').datetimepicker(datetimeConfig);
        $('#start_update_time').datetimepicker(datetimeConfig);
        $('#end_update_time').datetimepicker(datetimeConfig);
        $('#start_finish_time').datetimepicker(datetimeConfig);
        $('#end_finish_time').datetimepicker(datetimeConfig);
        $('#start_date').datetimepicker(datetimeConfig);
        $('#end_date').datetimepicker(datetimeConfig);
        $('#start_inserttime').datetimepicker(datetimeConfig);
        $('#end_inserttime').datetimepicker(datetimeConfig);
        $('#start_updatetime').datetimepicker(datetimeConfig);
        $('#end_updatetime').datetimepicker(datetimeConfig);
        $('#start_date').datetimepicker(datetimeConfig);
        $('#end_date').datetimepicker(datetimeConfig);
        hideProgress();
    }

   $('#queryform').submit(function() {
        var params = getParams();

        var url = GLOBAL_CONF['action_query'];
        showProgress();

        $.ajax(
            {
                "url": url,
                "type": "post",
                "data" : params,
                "dataType" : "html",
                "error" : function (jqXHR, textStatus, errorThrown)
                {
                    hideProgress();
                    var errMsg = errorThrown === 'Forbidden' ? "亲，没权限呢!" : "亲，服务器忙着呢!";
                    jAlert(errMsg, "提示");
                },
                "success" : function (data)
                {
                    hideProgress();
                    $("#query_result").html(data);
                    bindEvt(true);
                }
            }
        );

        return false;
    });


    //bind分页及其他事件
    bindEvt(false);
});

//查询结果(page为0表示当前页)
function queryResult (start)
{
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
            var errMsg = errorThrown === 'Forbidden' ? "亲，没权限呢!" : "亲，服务器忙，请稍后!"; jAlert(errMsg, "提示");
        },
        "success" : function (data) {
            $("#query_result").html(data);
            hideProgress();
            bindEvt(true);
        }
    });
}

function bindEvt(needUniform)
{
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
                    var errMsg = errorThrown === 'Forbidden' ? "亲，没权限呢!" : "亲，服务器忙!"; jAlert(errMsg, "提示");
                },
                "success" : function (data) {
                    if (data['code'] !== 0) {
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
}

function showProgress()
{
    $('#loaders').show();
}

function hideProgress()
{
    $('#loaders').hide();
}


function getParams ()
{
    var params = { };
    for (var key in GLOBAL_CONF['params_key'])
        params[key] = $('#'+key).val();

    return params;
}

function query_result (sortby)
{
    var params = getParams();
    params['sortby'] = sortby;
    params['asc'] = 1-params['asc'];

    var url = GLOBAL_CONF['action_query'];
    showProgress();

    $.ajax(
        {
            "url": url,
            "type": "post",
            "data" : params,
            "dataType" : "html",
            "error" : function (jqXHR, textStatus, errorThrown)
            {
                hideProgress();
                var errMsg = errorThrown === 'Forbidden' ? "亲，没权限呢!" : "亲，服务器忙着呢!";
                jAlert(errMsg, "提示");
            },
            "success" : function (data)
            {
                hideProgress();
                $("#query_result").html(data);
                bindEvt(true);
            }
        }
    );

    return false;
}

