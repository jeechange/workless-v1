+function ($) {

    var objURL = function (url) {
        this.ourl = url || window.location.href;
        this.href = "";//?前面部分
        this.params = {};//url参数对象
        this.jing = "";//#及后面部分
        this.init();
    };
    objURL.prototype.init = function () {
        var str = this.ourl;
        var index = str.indexOf("#");
        if (index > 0) {
            this.jing = str.substr(index);
            str = str.substring(0, index);
        }
        index = str.indexOf("?");
        if (index > 0) {
            this.href = str.substring(0, index);
            str = str.substr(index + 1);
            var parts = str.split("&");
            for (var i = 0; i < parts.length; i++) {
                var kv = parts[i].split("=");
                this.params[kv[0]] = kv[1];
            }
        }
        else {
            this.href = this.ourl;
            this.params = {};
        }
    };
    objURL.prototype.url = function () {
        var strurl = this.href;
        var objps = [];//这里用数组组织,再做join操作
        for (var k in this.params) {
            if (this.params[k]) {
                objps.push(k + "=" + this.params[k]);
            }
        }
        if (objps.length > 0) {
            strurl += "?" + objps.join("&");
        }
        if (this.jing.length > 0) {
            strurl += this.jing;
        }
        return strurl;
    };
    $(function () {
        $(".element-items .more").click(function () {
            var text = $(this).text();
            if (text == "更多") {
                $(this).parent().removeClass("overflow-hide");
                $(this).text("隐藏");
            } else {
                $(this).parent().addClass("overflow-hide");
                $(this).text("更多");
            }

        });

        $(".element-choice-item input").click(function () {
            var thisUrl = new objURL();
            var id = $(this).attr("data-key");
            var value = $(this).val();
            if (typeof thisUrl.params[id] == "undefined" || thisUrl.params[id] != value) {
                thisUrl.params[id] = value;
            } else {
                thisUrl.params[id] = -1;
            }
            location.href = thisUrl.url();
        });
        $(".element-choice-label input,.element-multi-label input").click(function () {
            var thisUrl = new objURL();
            var id = $(this).attr("data-key");
            thisUrl.params[id] = -1;
            location.href = thisUrl.url();
        });
        $(".element-multi-item input").click(function () {
            var thisUrl = new objURL();
            var id = $(this).attr("data-key");
            var value = $(this).val();
            if (typeof thisUrl.params[id] == "undefined" || thisUrl.params[id] == -1) {
                thisUrl.params[id] = value;
            } else {
                var queryValues = thisUrl.params[id].split("|");
                var isSelected = false;
                for (var j in queryValues) {
                    if (queryValues[j] == value) {
                        isSelected = true;
                        break;
                    }
                }
                var values = [];
                var items = $("input[data-key='" + id + "']");
                for (var i = 0; i <= items.length; i++) {
                    var $item = $(items[i]);
                    var itemVal = $item.val();
                    if (itemVal == value) {
                        isSelected || values.push(itemVal);
                        continue;
                    }
                    for (var k in queryValues) {
                        if (queryValues[k] == itemVal) {
                            values.push(itemVal);
                            break;
                        }
                    }
                }
                if (values.length == 0) {
                    thisUrl.params[id] = -1;
                } else {
                    thisUrl.params[id] = values.join("|");
                }
            }
            location.href = thisUrl.url();
        });
    });
}(jQuery);