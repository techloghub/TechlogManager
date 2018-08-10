//助手的js工具库
AJAX_ERROR_FUNC = function(jqXHR, textStatus, errorThrown) {
    _hideProgress();
    var errMsg = errorThrown == 'Forbidden' ? "亲，没权限呢!" : "亲，服务器忙!";
    alert(errMsg);
};

//调用ajax提交
ajax_submit = function(url, params, callback, dataType) {
    dataType = typeof(dataType) == "undefined" ? "json" : dataType;
    $.ajax({
        "url": url,
        "type": "post",
        "data": params,
        "dataType": dataType,
        "error": AJAX_ERROR_FUNC,
        "success": callback
    });
}

_showProgress = function() {
    $('#loaders').show();
}
_hideProgress = function() {
    $('#loaders').hide();
}

//构建时间选择
var datetimeConfig = {
    dateFormat: 'yy-mm-dd',
    timeFormat: 'HH:mm:ss',
    showSecond: true
};


//*********************** ajax分页功能 ************************
initPagination = function($resultDiv, url, paramFunc) {
    var bindPageEvent = function(p) {
        var $pagination = $(".pagination");
        $pagination.jqPagination({
            current_page: p,
            ///max_page: pages,
            page_string: '当前页 {current_page} 共 {max_page} 页',
            paged: function(page) {
                pageQuery(page);
            }
        });
    }
    var pageQuery = function(p) {
        var params = paramFunc(); //调用函数获取查询对象
        params["p"] = p;
        _showProgress();
        ajax_submit(url, params, function(data) {
            $resultDiv.html(data);
            bindPageEvent(p);
            _hideProgress();
        }, "html");
    }
    bindPageEvent(1);

    return {
        "pageQuery": pageQuery
    };
};

//dom ready process
$(document).ready(function() {
    //设置日期
    $('.datetimepicker').datetimepicker(datetimeConfig);
});

