(function ($) {


    var H5Upload = function () {

    };

    H5Upload.prototype = {
        chunkSize: 64 * 1024,
        handleInputChange: function () {

        }
    };


    //扩展这个方法到jquery
    $.fn.extend({

        //插件名字
        pluginname: function () {

            //遍历匹配元素的集合
            return this.each(function () {

                //在这里编写相应代码进行处理

            });
        }
    });

    //传递jQuery到方法中，这样我们可以使用任何javascript中的变量来代替"$"
})(jQuery);

