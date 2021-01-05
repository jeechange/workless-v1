+function ($) {
    'use strict';
    var phpexForm = function (element, options) {
        this.$element = $(element);
        this.options = options;
        this.element = element;
        this.lastValue = {};
        this.initialize();
    };



    phpexForm.rulesExtend = {
    };

    phpexForm.rules = {
        requir: function (obj, name, rule) {
            return /^\S+$/.test(this.getValue(name, 1));
        },
        unique: function (obj, name, rule) {
            if (this.lastValue[name] && this.getValue(name) == this.lastValue[name]['value'])
                return this.lastValue[name]['validate'];
            if (!this.getValue(name)) {
                this.lastValue[name] = {"value": this.getValue(name), "validate": true};
                return true;
            }
            var tid = this.format(phpexForm.DEFAULTS.tip_prefix, name);
            $(tid).show().addClass("phpexForm-loading").text("正在检测..");
            var uni = false;
            $.ajax({
                type: "POST", //用post方式
                url: rule[2],
                async: false, //同步加载
                data: {param: name, value: this.getValue(name)}, //发送的数据
                dataType: "json",
                success: function (msg) {
                    uni = msg.status == "y" ? true : false;
                },
                complete: function (XHR, status) {
                    if (XHR.status == 200) {
                        return;
                    }
                    if (XHR.status == 401) {
                        alert("权限不足，无法操作");
                        return;
                    }
                    if (XHR.status == 404) {
                        alert("请求失败");
                        return;
                    }
                    alert("错误:" + XHR.status);
                }
            });
            this.lastValue[name] = {"value": this.getValue(name), "validate": uni};
            return uni;
        },
        email: function (obj, name, rule) {
            if (!this.getValue(name))
                return true;
            return /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(this.getValue(name, 1));
        },
        tel: function (obj, name, rule) {
            if (!this.getValue(name))
                return true;
            return /^1[3-8]\d{9}$/.test(this.getValue(name, 1));
        },
        url: function (obj, name, rule) {
            if (!this.getValue(name))
                return true;
            return /^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/.test(this.getValue(name, 1));
        },
        currency: function (obj, name, rule) {
            if (!this.getValue(name))
                return true;
            return /^\d+(\.\d+)?$/.test(this.getValue(name, 1));
        },
        num: function (obj, name, rule) {
            if (!this.getValue(name))
                return true;
            return /^\-?\d+$/.test(this.getValue(name, 1));
        },
        num1: function (obj, name, rule) {
            if (!this.getValue(name))
                return true;
            return /^\d+$/.test(this.getValue(name, 1));
        },
        num2: function (obj, name, rule) {
            if (!this.getValue(name))
                return true;
            return /^\-\d+$/.test(this.getValue(name, 1));
        },
        decimal: function (obj, name, rule) {
            if (!this.getValue(name))
                return true;
            return /^\-?\d+(\.\d{1,2})?$/.test(this.getValue(name, 1));
        },
        float: function (obj, name, rule) {
            if (!this.getValue(name))
                return true;
            return /^\-?\d+(\.\d+)?$/.test(this.getValue(name, 1));
        },
        len: function (obj, name, rule) {
            if (!this.getValue(name))
                return true;
            var value = this.getValue(name);
            var l = 0;
            if (typeof value == 'string') {
                var a = value.split("");
                for (var i = 0; i < a.length; i++) {
                    if (a[i].charCodeAt(0) < 299) {
                        l++;
                    } else {
                        l += 2;
                    }
                }
                return l >= rule[1][0] && l <= rule[1][1];
            } else if (typeof value == 'object') {
                for (var i in value) {
                    l++;
                }
            }
            return l >= rule[1][0] && l <= rule[1][1];
        },
        confirm: function (obj, name, rule) {
            return this.getValue(name) == $(this.format(phpexForm.DEFAULTS.prefix, rule[1])).val();
        },
        equal: function (obj, name, rule) {
            return this.getValue(name) == rule[1];
        },
        min: function (obj, name, rule) {
            if (!this.getValue(name))
                return true;
            var value = this.getValue(name);
            var l = 0;
            if (typeof value == 'string')
                l = parseInt(value);
            else if (typeof value == 'object') {
                for (var i in value) {
                    l++;
                }
            }
            return l >= rule[1];

        },
        max: function (obj, name, rule) {
            if (!this.getValue(name))
                return true;
            var value = this.getValue(name);
            var l = 0;
            if (typeof value == 'string')
                l = parseInt(value);
            else if (typeof value == 'object') {
                for (var i in value) {
                    l++;
                }
            }
            return l <= rule[1];
        },
    };

    phpexForm.prototype.format = function (foramat) {
        var args = arguments;
        var i = 0;
        return foramat.replace(/%[%bcdfosxX]/g, function (match) {
            switch (match) {
                case '%%':
                    return "%";
                case '%b':
                    i++;
                    return parseInt(args[i], 2).toString();
                case '%c' :
                    i++;
                    return args[i].charCodeAt();
                case '%d' :
                    i++;
                    return parseInt(args[i], 10).toString();
                case '%f' :
                    i++;
                    return parseFloat(args[i]).toString();
                case '%o' :
                    i++;
                    return parseInt(args[i], 8).toString();
                case '%s' :
                    i++;
                    return args[i].toString();
                case '%x' :
                    i++;
                    return parseInt(args[i], 16).toString().toLowerCase();
                case '%X' :
                    i++;
                    return parseInt(args[i], 16).toString().toUpperCase();
            }
        });
    };

    phpexForm.prototype.initialize = function () {
        for (var i in this.options) {
            var id = this.format(phpexForm.DEFAULTS.prefix, i);
            var obj = $(id);
            if (obj.length == 0)
                continue;
            var msg_id = this.format(phpexForm.DEFAULTS.tip_prefix, i);
            var msg_obj = $(msg_id);
            if (msg_obj.length == 0) {
                msg_obj = $(this.format('<span id="frm_tip_msg_frm_%s" class="frm-tip-msg"></span>', i));
                obj.after(msg_obj);
            }
            if (obj[0].nodeName.toLowerCase() == "fieldset") {
                var elems = obj.children("[phpex-item]");
                for (var item = 0; item < elems.length; item++) {
                    var elem = $(elems[item]);
                    elem.on('focus', $.proxy(this.inputHint, this, elem, i));
                    elem.on('change', $.proxy(this.validate, this, elem, i));
                    if (elem.attr('event-keyup') == 'on') {
                        elem.on('keyup', $.proxy(this.validate, this, elem, i));
                    }
                }
            } else {
                obj.on('focus', $.proxy(this.inputHint, this, obj, i));
                obj.on('change', $.proxy(this.validate, this, obj, i));
                if (obj.attr('event-keyup') == 'on') {
                    obj.on('keyup', $.proxy(this.validate, this, obj, i));
                }
            }
        }
        var that = this;
        this.$element.bind('submit', function () {
            return that.allValidate();
        });
    };

    phpexForm.prototype.inputHint = function (obj, name) {
        var value = this.getValue(name);
        if (value) {
            return;
        }
        var tid = this.format(phpexForm.DEFAULTS.tip_prefix, name);
        if ($(tid).length > 0) {
            $(tid).show().removeClass('phpexForm-success').removeClass('phpexForm-error').text(this.options[name][0] ? this.options[name][0][0] : phpexForm.DEFAULTS.hint);
        }
    };

    phpexForm.prototype.validate = function (obj, name) {
        var validate = true;
        var error = "";
        var hint = this.options[name][0] ? this.options[name][0] : [null, null, null];
        for (var rule in this.options[name]) {
            if (rule == 0)
                continue;
            if (typeof phpexForm.rules[rule] == "function") {
                validate = phpexForm.rules[rule].call(this, obj, name, this.options[name][rule]);
            } else if (typeof phpexForm.rulesExtend[rule] == "function") {
                validate = phpexForm.rulesExtend[rule].call(this, obj, name, this.options[name][rule]);
            } else {
                error = "规则未定义：" + rule;
            }
            if (!validate || error) {
                this.showError(error || this.options[name][rule][0] || hint[2], name, obj);
                return false;
            }
        }
        this.showSuccess(hint[1] || phpexForm.DEFAULTS.success, name, obj);
        return true;
    };

    phpexForm.prototype.showError = function (error, name, obj) {
        var tid = this.format(phpexForm.DEFAULTS.tip_prefix, name);
        obj.addClass('phpexForm-validate-error').removeClass('phpexForm-validate-success').removeClass('phpexForm-loading');
        if ($(tid).length > 0) {
            $(tid).show().removeClass('phpexForm-success').addClass("phpexForm-error").text(error || phpexForm.DEFAULTS.error);
        } else {
            alert(error || phpexForm.DEFAULTS.error);
        }
    };

    phpexForm.prototype.showSuccess = function (success, name, obj) {
        var tid = this.format(phpexForm.DEFAULTS.tip_prefix, name);
        obj.addClass('phpexForm-validate-success').removeClass('phpexForm-validate-error').removeClass('phpexForm-loading');
        if ($(tid).length > 0) {
            $(tid).show().removeClass('phpexForm-error').addClass("phpexForm-success").text(success ? success : "正确");
        }
    };

    phpexForm.prototype.allValidate = function () {
        var validate = true;
        for (var i in this.options) {
            var id = this.format(phpexForm.DEFAULTS.prefix, i);
            var obj = $(id);
            if (obj.length == 0)
                continue;
            if (obj[0].nodeName.toLowerCase() == "fieldset") {
                var elems = obj.children("[phpex-item]");
                for (var item in elems) {
                    var elem = $(elems[item]);
                    validate = this.validate(elem, i);
                    if (!validate) {
                        elem.trigger("focus");
                        return false;
                    }
                }
            } else {
                validate = this.validate(obj, i);
                if (!validate) {
                    obj.trigger("focus");
                    return false;
                }
            }
        }
        return true;
    };


    phpexForm.prototype.getValue = function (name, type) {
        var id = this.format(phpexForm.DEFAULTS.prefix, name);
        var obj = $(id);
        if (obj.length == 0)
            return "";
        if (obj[0].nodeName.toLowerCase() != "fieldset") {
            return obj.val();
        }
        var elems = obj.children("[phpex-item]");
        var r = {};
        for (var item = 0; item < elems.length; item++) {
            var elem = $(elems[item]);
            if (elem.attr("type") == "radio" && elem.attr("checked")) {
                r[elem.attr('phpex-item')] = elem.val();
            } else if (elem.attr("type") == "checkbox" && elem.attr("checked")) {
                r[elem.attr('phpex-item')] = elem.val();
            } else {
                r[elem.attr('phpex-item')] = elem.val();
            }
        }
        return type == 1 ? r.join(",") : r;
    };

    phpexForm.DEFAULTS = {
        prefix: '#frm_%s',
        tip_prefix: '#frm_tip_msg_frm_%s',
        hint: "请输入信息",
        success: "正确",
        error: "错误"
    };

    $.fn.phpexForm = function (option) {
        return this.each(function () {
            var $this = $(this);
            var data = $this.data('phpex.phpexForm');
            var options = $.extend({}, $this.data(), typeof option == 'object' && option);
            if (!data)
                $this.data('bs.modal', (data = new phpexForm(this, options)));
            if (typeof option == 'string')
                data[option].call($this);
        });
    };

}(jQuery);