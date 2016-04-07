/**
 * Created by toplan on 15/7/11.
 */

(function($){
    var config = {};

    $.loading = function (options) {

        var opts = $.extend(
            $.loading.default,
            options
        );

        config = opts;
        init(opts);

        var selector = '#' + opts.id;

        var timer;

        $(document).on('ajaxStart', function(){
            if (config.ajax) {                     
                timer = setTimeout(function()
                {
                    $(selector).show();
                },
                500);
                //setTimeout(function(){                
                    
                //}, opts.minTime);                                       
            }
        });        

        $(document).on('ajaxComplete', function(){
            clearTimeout(timer);
            $(selector).hide();
        });

        return $.loading;
    };

    $.loading.open = function (time) {
        var selector = '#' + config.id;
        //$(selector).show();
        $(selector).hide();
        //if (time) {
        //    setTimeout(function(){
        //        $(selector).hide();
        //    }, parseInt(time));
        //}
    };

    $.loading.close = function () {
        var selector = '#' + config.id;
        $(selector).hide();
    };

    $.loading.ajax = function (isListen) {
        config.ajax = isListen;
    };

    $.loading.default = {
        ajax       : true,
        //wrap div
        id         : 'ajaxLoading',
        zIndex     : '1000',
        background : 'rgba(0, 0, 0, 0)',
        minTime    : 500,
        radius     : '4px',
        width      : '85px',
        height     : '85px',

        //loading img/gif
        imgPath    : 'img/ajax-loading.gif',
        imgWidth   : '45px',
        imgHeight  : '45px',

        //loading text
        tip        : 'loading...',
        fontSize   : '14px',
        fontColor  : '#fff'
    };

    function init (opts) {
        //wrap div style
        var wrapCss = 'display: none;position: fixed;top: 0;bottom: 0;left: 0;right: 0;margin: auto;padding: 8px;text-align: center;vertical-align: middle;';
        var cssArray = [
            'width:' + opts.width,
            'height:' + opts.height,
            'z-index:' + opts.zIndex,
            'background:' + opts.background,
            'border-radius:' + opts.radius
        ];
        wrapCss += cssArray.join(';');

        //img style
        var imgCss = 'margin-bottom:8px;';
        cssArray = [
            'width:' + opts.imgWidth,
            'height:' + opts.imgWidth
        ];
        imgCss += cssArray.join(';');

        //text style
        var textCss = 'margin:0;';
        cssArray = [
            'font-size:' + opts.fontSize,
            'color:'     + opts.fontColor
        ];
        textCss += cssArray.join(';');

        var html = '<div id="' + opts.id + '" style="' + wrapCss + '">'
                  +'<img src="' + opts.imgPath + '" style="' + imgCss + '">'
                  +'<p style="' + textCss + '">' + opts.tip + '</p></div>';

        var html = ' <div id="' + opts.id + '" style="' + wrapCss + '"> \
                        <div class="cssload-container"> \
                            <div class="cssload-whirlpool"></div> \
                        </div> \
                    </div>';
        

        $(document).find('body').append(html);
    }

})(window.jQuery||window.Zepto);
