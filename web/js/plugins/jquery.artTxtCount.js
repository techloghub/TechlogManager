/* tangbin - http://www.planeArt.cn - MIT Licensed */
(function($) {
    // tipWrap: 	提示消息的容器
    // maxNumber: 	最大输入字符
    $.fn.artTxtCount = function(tipWrap, maxNumber) {
        var countClass = 'js_txtCount', // 定义内部容器的CSS类名
            fullClass = 'js_txtFull', // 定义超出字符的CSS类名
            disabledClass = 'disabled';		// 定义不可用提交按钮CSS类名

        // 统计字数
        var count = function() {
            var btn = $(this).closest('form').find('#submit_confirm'),
                val = $(this).val().length,
                // 是否禁用提交按钮
                disabled = {
                on: function() {
                    btn.removeAttr('disabled').removeClass(disabledClass);
                },
                off: function() {
                    btn.attr('disabled', 'disabled').addClass(disabledClass);
                }
            };

            if (val == 0)
                disabled.off();
            if (val <= maxNumber) {
                if (val > 0)
                    disabled.on();
                tipWrap.html('<span class="' + countClass + '">还能输入 <strong>' + (maxNumber - val) + '</strong> 个字</span>');
            } else {
                disabled.off();
                tipWrap.html('<span class="' + countClass + ' ' + fullClass + '">已经超出 <strong>' + (val - maxNumber) + '</strong> 个字</span>');
            }
            ;
        };
        $(this).bind('keyup change', count);

        return this;
    };
})(jQuery);

