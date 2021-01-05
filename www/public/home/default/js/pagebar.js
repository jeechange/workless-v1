var objURL = function (url) {
    this.ourl = url || window.location.href;
    this.href = "";//?前面部分
    this.params = {};//url参数对象
    this.jing = "";//#及后面部分
    this.init();
};
//分析url,得到?前面存入this.href,参数解析为this.params对象，#号及后面存入this.jing
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
//只是修改this.params
objURL.prototype.set = function (key, val) {
    this.params[key] = val;
};
//只是设置this.params
objURL.prototype.remove = function (key) {
    this.params[key] = undefined;
};
//根据三部分组成操作后的url
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
//得到参数值
objURL.prototype.get = function (key) {
    return this.params[key];
};
function changePageSize(obj, vars) {
    var index = obj.selectedIndex; // 选中索引
    var value = obj.options[index].value; // 选中值
    document.cookie = 'page_size_' + vars + "=" + value;
    window.location.reload();
}
function jumpToPage(vars, page) {
    var thisUrl = new objURL();
    thisUrl.set(vars, page);
    location.href = thisUrl.url();
}