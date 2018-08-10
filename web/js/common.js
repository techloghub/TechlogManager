// 连接按钮
$("input[data-type=button-link]").on('click', function(event){
    event.preventDefault();
    var target = event.target;
    window.location=$(target).attr("data-link");
});

$("button[data-type=button-link]").on('click', function(event){
    event.preventDefault();
    var target = event.target;
    window.location=$(target).attr("data-link");
});


var submitCount = 0;
// 表单ajax提交
$("form[data-type=form-submit]").on('submit', function(event){
    event.preventDefault();
    if (submitCount == 0) {
        submitCount++;
    } else {
        alert("请不要重复提交表单");
        return false;
    }
    var target = event.target;
    var submitHint = $(target).attr("submit-hint");
    var answer = true;
    if (submitHint) {
        answer = confirm(submitHint);
    }
    if (answer) {
        var action = $(target).attr("submit-action");
        $.ajax({
            url : action,
            data : $(target).serialize(),
            type : 'post',
            dataType : 'json',
            success : function(data) {
                if (data.code == 0) {
                    var successhint = $(target).attr("success-hint");
                    if (successhint != null && successhint != ""){
                        alert(successhint);
                    }
                    if ($(target).attr("submit-redirect")) {
                        if ($(target).attr("submit-redirect") == "#") {
                            location.reload();
                        }
                        location.href = $(target).attr("submit-redirect");
                    } else {
                        // 回退到上一个页面
                        //location.href = document.referrer;
                    }
                } else {
                    alert(data.message);
                }
                submitCount = 0;
            }
        });
    }
});

function ajaxSubmitCallback(event) {
	event.preventDefault();
    var target = event.target;
    var submitHint = $(target).attr("ajax-hint");
    var answer = true;
    if (submitHint) {
        answer = confirm(submitHint);
    }
    if (answer) {
        var action = $(target).attr("ajax-action");
        $.ajax({
            url : action,
            type : 'get',
            dataType : 'json',
            success : function(data) {
                if (data.code == 0) {
                    var successhint = $(target).attr("success-hint");
                    if (successhint != null && successhint != ""){
                        alert(successhint);
                    }
                    if ($(target).attr("callback")) {
	                    var callback = $(target).attr("callback");
	                    window[callback](target);
                    } else {
	                    if ($(target).attr("submit-redirect")) {
		                    if ($(target).attr("submit-redirect") == "#") {
	                            location.reload();
	                        }
	                        location.href = $(target).attr("submit-redirect");
	                    } else {
	                        location.href = document.referrer;
	                    }
	                }
                } else {
                    alert(data.message);
                }
            }
        });
    }
    return false;
}

$('#query_result').on('click', 'a[data-type=ajax-submit]', ajaxSubmitCallback);

$("button[data-type=cbox-inline]").colorbox({inline:true, width:"35%"});

$("select[data-type=select-direct]").on('change', function(event){
    event.preventDefault();
    var target = event.target;
    var direct = $(target).find(':selected').attr('direct-action');
    location.href = direct;
});

// 通用的列表处理
$(document).ready(function() {
    var showProgress = function() {
        $('#loaders').show();
    }
    var hideProgress = function() {
        $('#loaders').hide();
    }

    var datetimeConfig = {
        dateFormat: 'yy-mm-dd',
		timeFormat: 'HH:mm:ss',
		showSecond: true
    };
    $('#create_time_min').datetimepicker(datetimeConfig);
    $('#create_time_max').datetimepicker(datetimeConfig);
    hideProgress();
	
	var getParams = function() {
		var queryParams = GLOBAL_CONF['query_params'],
            params = {};
        for( var k in queryParams) {
            params[k] = $.trim($(queryParams[k]).val());
        }
		return params;
	}

    $('#queryform').submit(function(evt) {
		evt.preventDefault();
        
        var url    = GLOBAL_CONF['action_query'],
			params = getParams();
		
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
            $("#query_result").find('input:checkbox, input:radio, select.uniformselect').uniform();
        }

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
	
	
	// 批量操作相关
	$('#query_result').on('click', '#all_select', function() {
		var val = $(this).attr("checked");
		if(val == undefined) {
				$('td .selected').removeAttr("checked");
				$('td span').removeClass('checked');
		} else {
				$('td .selected').attr("checked", true);
				$('td span').addClass('checked');
		}
	});

	$('#query_result').on('click', '.yes_submit', function() {
		var state = $(this).data("state");
		if (state == undefined) {
			state = 1;
		}
		doSubmit(state, this);
	});

	$('#query_result').on('click', '.no_submit', function() {
		var state = $(this).data("state");
		if (state == undefined) {
			state = 2;
		}
		doSubmit(state, this);
	});

	var doSubmit = function(state, that) {
		var $selected = $('td .selected:checked');
		if ($selected.size() == 0) {
			alert("没有选中任何记录");
			return false;
		}

		if (!confirm("确定要批量操作？")) {
			return false;
		}
		
		var ids = [];
		$selected.each(function() {
			ids.push($(this).attr('data-id'));
		});
		
		var url = $(that).parent().data('url');
		$.post(
			url,
			{ids: ids, state: state},
			function(data){
				if (data.code == 0) {
						location.reload();
				} else {
						alert(data.message);
				}
			}
		);
	}
});
