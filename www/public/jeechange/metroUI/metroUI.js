//菜单初始化

init = {
    readys: [],
    suplyId: 0,
    clearReady: function () {
        this.readys = [];
    }, ready: function (callback) {
        this.readys.push(callback);
    }, commit: function () {
        for (var i in this.readys) {
            if (typeof this.readys[i] == "function") this.readys[i]();
        }
    }
};

function initMenu(notPage) {
    var hash = location.hash;
    if (!hash || hash == "#" || hash == "#/") {
        var base_url = $("[data-menu-id='1']").attr("href") + "#1";
    } else {
        var base_url = hash.substring(1);
    }
    var menuOpenClose = getCookie("menuOpenClose");

    if (menuOpenClose) {
        $("#menudiv").removeClass("menu-2");
        $(".index-header").removeClass("index-header-2");
        $("#contentdiv").removeClass("contentdiv-2");
        $("#contentloding").removeClass("contentloding-2");
        $(".left-menu").removeClass("left-menu-2");
    }


    var initUrl = new objURL(base_url);
    var hasid = initUrl.hash;
    if (hasid && hasid != "#" && !$("#menudiv").hasClass("menu-2")) {
        hasid = hasid.substring(1);
        var selectedMenu = $("[data-menu-id='" + hasid + "']");
        if (selectedMenu.length > 0) {
            var $li = selectedMenu.parent().length > 0 ? selectedMenu.parent() : selectedMenu.parents("li");
            selectedMenu.parents("li").addClass("current").siblings(".current").removeClass("current");
            var sub_menu_3_4 = $li.find(".sub_menu_3_4");
            $(".sub_menu_3_4").addClass("hide");
            if (sub_menu_3_4.length > 0) {
                $("#contentdiv").addClass("content_show_3_4");
                sub_menu_3_4.removeClass("hide");
            }
        }
    } else if (hasid && hasid !== "#" && $("#menudiv").hasClass("menu-2")) {
        hasid = hasid.substring(1);
        var selectedMenu = $("[data-menu-id='" + hasid + "']");

        $(".sub-menu-2").css("visibility", "hidden");
        if (selectedMenu.length > 0) {
            var $li = selectedMenu.parent("li.main-menu-li").length > 0 ? selectedMenu.parent("li.main-menu-li") : selectedMenu.parents("li.main-menu-li");
            selectedMenu.parents("li").addClass("current").siblings(".current").removeClass("current");
            var sub_menu_2 = $li.find(".sub-menu-2");
            if (sub_menu_2.length > 0) {
                sub_menu_2.css("visibility", "visible");
                $("#contentdiv").addClass("content_show_3_4");
            }
        }
    }
    if (!notPage) getPage(initUrl.url());
}

function changeMenu(menuId) {
    if (!menuId) return;
    setUrl("menu_id", menuId);
    initMenu(true);
}

function changeMenu2(menuId) {
    var selectedMenu = $("[data-menu-id='" + menuId + "']");
    if (selectedMenu.length > 0) {
        if (selectedMenu.hasClass("menu_main_a")) {
            var $nextul = $(selectedMenu).next("ul");
            $nextul.show();
            var parli = $(selectedMenu).parent("li");
            parli.siblings(".current").removeClass("current").children("ul").hide();
            var menu_2_item = $nextul.find(".menu_2_item:first");
            if (menu_2_item.length > 0) {
                menu_2_item.addClass("current").siblings(".current").removeClass("current");
                var sub_menu_3_4 = $(menu_2_item).find(".sub_menu_3_4");
                if (sub_menu_3_4.length > 0) {
                    sub_menu_3_4.show();
                    $("#contentdiv").addClass("content_show_3_4");
                    var active_menu_3_a = $(menu_2_item).siblings(".sub_menu_3_4").find(".menu_3_a:first");
                    if (active_menu_3_a.length > 0) {
                        active_menu_3_a.parent().addClass("current").siblings(".current").removeClass("current");
                    }
                    var $menu_4_a = $(menu_2_item).siblings(".sub_menu_3_4").find(".menu_4_a:first");
                    if ($menu_4_a.length > 0) {
                        $menu_4_a.parent().addClass("current").siblings(".current").removeClass("current");
                    }
                } else {
                    $("#contentdiv").removeClass("content_show_3_4");
                }
            } else {
                $("#contentdiv").removeClass("content_show_3_4");
            }
        } else if (selectedMenu.hasClass("menu_2_a")) {
            var parli = $(selectedMenu).parent("li");
            parli.addClass("current");
            parli.siblings(".current").removeClass("current");
            $(".sub_menu_3_4").addClass("hide");
            var sub_menu_3_4 = $(selectedMenu).siblings(".sub_menu_3_4");
            if (sub_menu_3_4.length > 0) {
                $(selectedMenu).siblings(".sub_menu_3_4").removeClass("hide");
                $("#contentdiv").addClass("content_show_3_4");
                var active_menu_3_a = $(selectedMenu).siblings(".sub_menu_3_4").find(".menu_3_a:first");
                if (active_menu_3_a.length > 0) {
                    active_menu_3_a.parent().addClass("current").siblings(".current").removeClass("current");
                }
                var $menu_4_a = $(selectedMenu).siblings(".sub_menu_3_4").find(".menu_4_a:first");
                if ($menu_4_a.length > 0) {
                    $menu_4_a.parent().addClass("current").siblings(".current").removeClass("current");
                }
            } else {
                $("#contentdiv").removeClass("content_show_3_4");
            }

            var menu_main = parli.parents("li");
            menu_main.children("ul").show();
            menu_main.addClass("current").siblings().removeClass("current").children("ul").hide();

        } else if (selectedMenu.hasClass("menu_3_a")) {
            var parli = $(selectedMenu).parent("li");
            parli.addClass("current");
            parli.siblings(".current").removeClass("current");

            var menu_2_item = $(selectedMenu).parents(".menu_2_item");
            menu_2_item.addClass("current");
            menu_2_item.siblings(".current").removeClass("current");
            $(".sub_menu_3_4").addClass("hide");
            var sub_menu_3_4 = menu_2_item.children(".sub_menu_3_4");
            if (sub_menu_3_4.length > 0) {
                sub_menu_3_4.addClass("hide");
                $("#contentdiv").addClass("content_show_3_4");
            } else {
                $("#contentdiv").removeClass("content_show_3_4");
            }
            var menu_main = menu_2_item.parents("li");
            menu_main.children("ul").show();
            menu_main.addClass("current").siblings().removeClass("current").children("ul").hide();
        }

    }
}


function changeMenu3(menuId) {
    if (!menuId) return;
    setUrl("menu_id", menuId);
    if ($("#menudiv").hasClass("menu-2")) {
        return changeMenu2(menuId);
    }
    try {
        var selectedMenu = $("[data-menu-id='" + menuId + "']");
        if (selectedMenu.length > 0) {
            if (selectedMenu.hasClass("menu_main_a")) {
                var $nextul = $(selectedMenu).next("ul");
                $nextul.show();
                var parli = $(selectedMenu).parent("li");
                parli.siblings(".current").removeClass("current").children("ul").hide();
                var menu_2_item = $nextul.find(".menu_2_item:first");
                if (menu_2_item.length > 0) {
                    menu_2_item.addClass("current").siblings(".current").removeClass("current");
                    var sub_menu_3_4 = $(menu_2_item).find(".sub_menu_3_4");
                    if (sub_menu_3_4.length > 0) {
                        sub_menu_3_4.show();
                        $("#contentdiv").addClass("content_show_3_4");
                        var active_menu_3_a = $(menu_2_item).siblings(".sub_menu_3_4").find(".menu_3_a:first");
                        if (active_menu_3_a.length > 0) {
                            active_menu_3_a.parent().addClass("current").siblings(".current").removeClass("current");
                        }
                        var $menu_4_a = $(menu_2_item).siblings(".sub_menu_3_4").find(".menu_4_a:first");
                        if ($menu_4_a.length > 0) {
                            $menu_4_a.parent().addClass("current").siblings(".current").removeClass("current");
                        }
                    } else {
                        $("#contentdiv").removeClass("content_show_3_4");
                    }
                } else {
                    $("#contentdiv").removeClass("content_show_3_4");
                }
            } else if (selectedMenu.hasClass("menu_2_a")) {
                var parli = $(selectedMenu).parent("li");
                parli.addClass("current");
                parli.siblings(".current").removeClass("current");
                $(".sub_menu_3_4").addClass("hide");
                var sub_menu_3_4 = $(selectedMenu).siblings(".sub_menu_3_4");
                if (sub_menu_3_4.length > 0) {
                    $(selectedMenu).siblings(".sub_menu_3_4").removeClass("hide");
                    $("#contentdiv").addClass("content_show_3_4");
                    var active_menu_3_a = $(selectedMenu).siblings(".sub_menu_3_4").find(".menu_3_a:first");
                    if (active_menu_3_a.length > 0) {
                        active_menu_3_a.parent().addClass("current").siblings(".current").removeClass("current");
                    }
                    var $menu_4_a = $(selectedMenu).siblings(".sub_menu_3_4").find(".menu_4_a:first");
                    if ($menu_4_a.length > 0) {
                        $menu_4_a.parent().addClass("current").siblings(".current").removeClass("current");
                    }
                } else {
                    $("#contentdiv").removeClass("content_show_3_4");
                }

                var menu_main = parli.parents("li");
                menu_main.children("ul").show();
                menu_main.addClass("current").siblings().removeClass("current").children("ul").hide();

            } else if (selectedMenu.hasClass("menu_3_a")) {
                var parli = $(selectedMenu).parent("li");
                parli.addClass("current");
                parli.siblings(".current").removeClass("current");

                var menu_2_item = $(selectedMenu).parents(".menu_2_item");
                menu_2_item.addClass("current");
                menu_2_item.siblings(".current").removeClass("current");
                $(".sub_menu_3_4").addClass("hide");
                var sub_menu_3_4 = menu_2_item.children(".sub_menu_3_4");
                if (sub_menu_3_4.length > 0) {
                    sub_menu_3_4.addClass("hide");
                    $("#contentdiv").addClass("content_show_3_4");
                } else {
                    $("#contentdiv").removeClass("content_show_3_4");
                }
                var menu_main = menu_2_item.parents("li");
                menu_main.children("ul").show();
                menu_main.addClass("current").siblings().removeClass("current").children("ul").hide();
            }
        }
    } catch (e) {
        console.log(e);
    }
}

var objURL = function (url) {
    this.ourl = url || window.location.href;
    this.init();
};
objURL.prototype.init = function () {
    var a = document.createElement('a');
    a.href = this.ourl;
    this.query = a.search;
    this.params = (function () {
        var ret = {},
            seg = a.search.replace(/^\?/, '').split('&'),
            len = seg.length, i = 0, s;
        for (; i < len; i++) {
            if (!seg[i]) {
                continue;
            }
            s = seg[i].split('=');
            ret[s[0]] = s[1];
        }
        return ret;
    })();
    this.hash = a.hash;
    this.path = a.pathname.replace(/^([^\/])/, '/$1');

};
objURL.prototype.url = function () {
    var strurl = this.path;
    var objps = [];//这里用数组组织,再做join操作
    for (var k in this.params) {
        if (this.params[k]) {
            objps.push(k + "=" + this.params[k]);
        }
    }
    if (objps.length > 0) {
        strurl += "?" + objps.join("&");
    }
    if (this.hash.length > 0) {
        strurl += this.hash;
    }
    return strurl;
};

function setUrl(types, value) {
    var hash = location.hash;
    var myURL = new objURL(hash.substring(1));
    switch (types) {
        case "url":
            var valueUrl = new objURL(value);
            myURL.path = valueUrl.path;
            myURL.params = valueUrl.params;
            break;
        case "path":
            myURL.path = value;
            break;
        case "params":
            myURL.params = value;
            break;
        case "query":
            myURL.query = value;
            myURL.params = (function () {
                var ret = {},
                    seg = value.replace(/^\?/, '').split('&'),
                    len = seg.length, i = 0, s;
                for (; i < len; i++) {
                    if (!seg[i]) {
                        continue;
                    }
                    s = seg[i].split('=');
                    ret[s[0]] = s[1];
                }
                return ret;
            })();
            break;
        case "menu_id":
            myURL.hash = "#" + value;
            break;
        default:
            myURL.params[types] = value;
    }
    var tourl = myURL.url();
    location.hash = tourl;
    return tourl;
}

function errorAndJump(message, toUrl, time) {
    layer.msg(message, {
        offset: 't',
        time: time * 1000,
        icon: 2
    });
    if (toUrl)
        setUrl("url", toUrl);
    var hash = location.hash;
    var myURL = new objURL(hash.substring(1));
    var url = myURL.url();
    getPage(url);
}

function sideErrorAndJump(message, toUrl, time) {
    layer.msg(message, {
        offset: 't',
        time: time * 1000,
        icon: 2
    });
    if (toUrl) {
        setUrl("url", toUrl);
        var hash = location.hash;
        var myURL = new objURL(hash.substring(1));
        var url = myURL.url();
        getPage(url, function () {
            hideSideForm(true);
        });
    }
}

function successAndJump(message, toUrl, time) {
    layer.msg(message, {
        offset: 't',
        time: time * 1000,
        icon: 1
    });
    if (toUrl)
        setUrl("url", toUrl);
    var hash = location.hash;
    var myURL = new objURL(hash.substring(1));
    var url = myURL.url();
    getPage(url);
}

function sideSuccessAndJump(message, toUrl, time) {
    layer.msg(message, {
        offset: 't',
        time: time * 1000,
        icon: 1
    });
    if (toUrl)
        setUrl("url", toUrl);
    var hash = location.hash;
    var myURL = new objURL(hash.substring(1));
    var url = myURL.url();
    getPage(url, function () {
        hideSideForm(true);
    });
}

function successAndReload(message, toUrl, time) {
    layer.msg(message, {
        offset: 't',
        time: time * 1000,
        icon: 1
    });
    if (toUrl)
        setUrl("url", toUrl);
    setTimeout(function () {
        location.reload();
    }, 1000);
}


function initForm(formClass, $url) {
    $('#contentdiv .' + formClass).validator("destroy");

    $('#contentdiv [data-toggle="rich"]').each(function () {
        var options = {
            items: [
                'source', '|', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                'insertunorderedlist', '|', 'emoticons', 'imageupload', 'filesupload', 'link', '|', 'fullscreen', 'about'
            ],
            uploadJson: $(this).attr("data-upload") || $.uploadJson,
            fileManagerJson: $(this).attr("data-browse") || $.fileManagerJson,
            allowImageUpload: false,
            readonlyMode: $(this).attr("readonly") || false,
            afterFocus: function () {
                $(this.srcElement).trigger("focus");
            },
            afterBlur: function () {
                this.sync();
                $(this.srcElement).trigger("blur");
            },
            uploaderOptions: {
                apply: "company",
                uid: 0,
                sid: init.suplyId,
            },
        };
        KindEditor.create(this, options);
    });

    initDateTime();
    initInputFind();
    initWorker();

    $("#contentdiv .submit-" + formClass).unbind().bind("click", function () {
        var holdSubmit = $(this).data("holdSubmit") || false;
        if (holdSubmit) return;
        var $this = $(this);
        $this.data("holdSubmit", true);
        $('#contentdiv .' + formClass).isValid(function (v) {
            var url = $('#contentdiv .' + formClass).attr("action") || $url;
            if (v) {
                if ($('#contentdiv .' + formClass).attr("method").toLowerCase() == "get") {
                    setUrl("url", url);
                    var formData = $('.' + formClass).serialize();
                    var formUrl = setUrl("query", formData);
                    getPage(formUrl, function () {
                        $this.data("holdSubmit", false);
                    });
                } else {
                    postPage(url, $('.' + formClass), function () {
                        $this.data("holdSubmit", false);
                    });
                }
            }
        });
    });
}

function initSideFrom(formClass, $url) {
    $('#contenteditsdiv .' + formClass).validator("destroy");
    $('#contenteditsdiv [data-toggle="rich"]').each(function () {
        var options = {
            items: [
                'source', '|', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                'insertunorderedlist', '|', 'emoticons', 'imageupload', 'filesupload', 'link', '|', 'fullscreen', 'about'
            ],
            uploadJson: $(this).attr("data-upload") || $.uploadJson,
            fileManagerJson: $(this).attr("data-browse") || $.fileManagerJson,
            allowImageUpload: false,
            readonlyMode: $(this).attr("readonly") || false,
            afterFocus: function () {
                $(this.srcElement).trigger("focus");
            },
            afterBlur: function () {
                this.sync();
                $(this.srcElement).trigger("blur");
            },
            uploaderOptions: {
                apply: "company",
                uid: 0,
                sid: init.suplyId,
            },
        };
        KindEditor.create(this, options);
    });
    initDateTime();
    initInputFind();
    initWorker();

    $("#contentedits .contenteditsSubmit").unbind().bind("click", function () {
        var holdSubmit = $(this).data("holdSubmit") || false;
        if (holdSubmit) return;
        var $this = $(this);
        $this.data("holdSubmit", true);
        $("#contenteditsdiv ." + formClass).isValid(function (v) {
            var url = $('#contenteditsdiv .' + formClass).attr("action") || $url;
            if (v) {
                var msgIndex = layer.msg("正在提交", {
                    offset: 't',
                    time: 0,
                    icon: 16
                });
                $.ajax({
                    type: $('#contenteditsdiv .' + formClass).attr("method").toLowerCase(),
                    dataType: "html",
                    url: url,
                    headers: {
                        ajaxExtraMethod: "SideForm"
                    },
                    data: $("#contenteditsdiv ." + formClass).serialize(),
                    complete: function (request) {
                        $this.data("holdSubmit", false);
                        layer.close(msgIndex);
                        $("#contenteditsdivRes").html(request.responseText);
                        $("#contentedits").attr("data-old-url", "")
                    }
                });
            }

        });
    });
}

function hideSideForm(closeAnimate) {
    if (!$('#contentedits').hasClass('contentedits-show')) return false;
    if (closeAnimate) {
        $('#contentedits').addClass('contentedits-hide');
        setTimeout(function () {
            $('#contentedits').removeClass('contentedits-show');
            $('#contentedits').removeClass('contentedits-hide');
        }, 10)
    } else {
        $('#contentedits').addClass('contentedits-hide-animate');
        setTimeout(function () {
            $('#contentedits').removeClass('contentedits-show');
            $('#contentedits').removeClass('contentedits-hide-animate');
        }, 500)
    }
}


function initList($url) {
    $(".stdtable tr:odd").addClass('odd');

    var myURL = new objURL($url);
    if (myURL.params.data_sort) {
        var sort_info = myURL.params.data_sort.split("|");
        if (sort_info.length == 1) sort_info[1] = "1";
        $("[data-sort]").removeClass("data-sort-asc").removeClass("data-sort-desc");
        $("[data-sort='" + sort_info[0] + "']").addClass(sort_info[1] == "1" ? "data-sort-asc" : "data-sort-desc");
    }

    var searchForm = $(".tableoptions.searchForm");

    if (searchForm.length > 0) {
        $(".tableoptions.searchForm>form").attr("action", $url).attr("onsubmit", "return searchListsForm(this);");
    }

    initDateTime();
    initInputFind();
    initWorker();

    $("[data-sort]").hover(function () {
        $(this).addClass("data-sort-default");
        return false;
    }, function () {
        $(this).removeClass("data-sort-default");
        return false;
    }).mousedown(function () {
        $(this).addClass("data-sort-action");
        return false;
    }).mouseup(function () {
        $(this).removeClass("data-sort-action");
        var keys = $(this).attr("data-sort");

        if (!myURL.params.data_sort || (keys + "|2") == myURL.params.data_sort) {
            myURL.params.data_sort = keys + "|1";
        } else {
            myURL.params.data_sort = keys + "|2";
        }
        var myURL2 = new objURL(location.hash.substring(1));
        myURL.hash = myURL2.hash;
        var url = myURL.url();
        location.hash = url;

        var layIndex = layer.msg("加载中..", {
            offset: 't',
            time: 0,
            icon: 16
        });

        getPage(url, function () {
            layer.close(layIndex);
        });
        return false;
    });
}


function getPage(toUrl, callback, intoDivFun, fromUrl) {

    $.ajax({
        type: "GET",
        dataType: "html",
        headers: {
            "ajax-referer": fromUrl
        },
        url: toUrl,
        beforeSend: function () {
            init.clearReady();
            $("#contentlodingpercentage").animate({
                width: "20%",
            }, 100);
        },
        complete: function (request) {
            $("#contentlodingpercentage").animate({
                width: "100%",
            }, 300, function () {
                $("#contentlodingpercentage").css("width", 0);
            });
            if (intoDivFun) intoDivFun(request);
            else {
                $("#contentdiv").html(request.responseText).load(function () {

                });
                init.commit();
            }
            if (callback) callback();
        }
    });
}

function postPage(toUrl, form, callback) {
    var msgIndex = layer.msg("正在提交", {
        offset: 't',
        time: 0,
        icon: 16
    });
    $.ajax({
        type: "POST",
        dataType: "html",
        url: toUrl,
        data: $(form).serialize(),
        complete: function (request) {

            layer.close(msgIndex);
            $("#contentdiv").html(request.responseText);
            if (callback) callback();
        }
    });
}


function searchPicStock(obj) {
    var loadObj = $(obj).next("span.loading");

    if (loadObj.length == 0) {
        loadObj = $("<span class='loading'></span>");
        $(obj).after(loadObj);
    }
    loadObj.css("display", "inline-block");
    var url = $(obj).attr("action");
    $.ajax({
        type: "GET",
        dataType: "html",
        data: $(obj).serialize(),
        url: url,
        complete: function (request) {
            if ($(obj).parent().hasClass("searchForm-pic-upload-stock-mul")) {
                $(".pic-upload-stock-cbox-mul").html(request.responseText);
            } else {
                $(".pic-upload-stock-cbox").html(request.responseText);
            }
        }
    });
    return false;
}

function searchListsForm(obj) {
    var url = $(obj).attr("action");
    var formUrl = new objURL(url);
    var loadObj = $(obj).next("span.loading");

    if (loadObj.length == 0) {
        loadObj = $("<span class='loading'></span>");
        $(obj).after(loadObj);
    }
    loadObj.css("display", "inline-block");

    var formValues = $(obj).serializeArray();
    for (var i in formValues) {
        formUrl.params[formValues[i]['name']] = formValues[i]['value'];
    }
    var toUrl = formUrl.url();
    setUrl("url", toUrl);
    getPage(toUrl);
    return false;
}

function changePageSize(obj, vars) {
    var index = obj.selectedIndex; // 选中索引
    var value = obj.options[index].value; // 选中值
    document.cookie = 'page_size_' + vars + "=" + value + ";path=/";
    var url = $(obj).parents(".page-bar").attr("data-url");
    if (vars == "pic_stock_index") {
        getPage(url, null, function (request) {
            $(".pic-upload-stock-cbox").html(request.responseText);
        });
    } else if (vars == "pic_stock_index_mul") {
        getPage(url, null, function (request) {
            $(".pic-upload-stock-cbox-mul").html(request.responseText);
        });
    } else {
        getPage(url);
    }
}

function jumpToPage(obj, vars, page) {
    if (typeof(page) == "undefined") {
        var index = obj.selectedIndex; // 选中索引
        page = obj.options[index].value; // 选中值
    } else if ($(obj).hasClass("page-disabled")) {
        return false;
    }
    var url = $(obj).parents(".page-bar").attr("data-url");
    var thisUrl = new objURL(url);
    thisUrl.params[vars] = page;
    url = thisUrl.url();
    if (vars == "pic_stock_index") {
        getPage(url, null, function (request) {
            $(".pic-upload-stock-cbox").html(request.responseText);
        });
    } else if (vars == "pic_stock_index_mul") {
        getPage(url, null, function (request) {
            $(".pic-upload-stock-cbox-mul").html(request.responseText);
        });
    } else {
        getPage(url);
    }
}


function initDateTime() {
    var initDate = {};
    $(".laydate").each(function () {
        var format = $(this).attr("format") || "YYYY-MM-DD hh:mm:ss";
        var id = $(this).attr("id");
        if (!$(this).hasClass("laydate-icon"))
            $(this).addClass("laydate-icon");
        var renderOption = {
            elem: '#' + id,
            btns: ['clear', 'now', 'confirm']
        };
        if (typeof($(this).attr("data-range")) != "undefined") {
            renderOption.range = $(this).attr("data-range") || "~";
        } else {
            renderOption.done = function (value, date, endDate) {
                date.month--;
                if (date.month == -1) {
                    date.month = 11;
                    date.year--;
                }
                var minid = $("[date-min='" + id + "']").attr("id");
                if (minid && initDate[minid]) {
                    initDate[minid].config.min = date; //开始日选好后，重置结束日的最小日期
                }
                var maxid = $("[date-max='" + id + "']").attr("id");
                if (maxid && initDate[maxid]) {
                    initDate[maxid].config.max = date; //结束日选好后，重置开始日的最大日期
                }
            };
        }
        switch (format) {
            case "YYYY":
                renderOption.type = 'year';
                break;
            case "YYYY-MM":
                renderOption.type = 'month';
                break;
            case "YYYY-MM-DD":
                renderOption.type = 'date';
                break;
            case "hh:mm:ss":
                renderOption.type = 'time';
                break;
            default:
                renderOption.type = 'datetime';
        }
        initDate[id] = laydate.render(renderOption);
    });
}

function removeFindItem() {
    var findItem = $(this).parent();
    if (findItem.siblings("input[type='hidden']").length > 0) {
        findItem.siblings("input[type='hidden']").val("");
        findItem.siblings("input[type='text']").attr("readonly", false).val("").show();
        findItem.hide();
    } else {
        var items = findItem.siblings(".input-auto-find-text");
        var datas = [];
        for (var i = 0; i < items.length; i++) {
            datas.push($(items[i]).attr("data-id"));
        }
        $(findItem).parent().siblings("input[type='hidden']").val(datas.join(","));
        findItem.remove();
    }
}

function initWorker() {
    $(".worker-added").unbind().bind("click", function () {
        $(this).siblings(".worker-added-box").slideToggle();
    });
    $(".worker-fold").unbind().bind("click", function () {
        var ulbox = $(this).siblings("ul");
        if (ulbox.length === 0) return;
        if (ulbox.hasClass("worker-fold-off")) {
            ulbox.removeClass("worker-fold-off");
            $(this).text("收起");
        } else {
            ulbox.addClass("worker-fold-off");
            $(this).text("展开");
        }
    });

    $(".worker-finder").unbind().bind("input", function () {
        var val = $(this).val().toString().replace(/^\s+|\s+$/gm, '');
        if (!val) {
            $(this).siblings(".worker-added-find-list").hide();
            $(this).siblings(".worker-added-list").show();
            return true;
        }
        var lists = $(this).siblings(".worker-added-find-list").find(".worker-keyword");
        for (var i = 0; i < lists.length; i++) {
            var text = $(lists[i]).text();
            var indexOf = text.indexOf(val);
            if (indexOf === -1) {
                $(lists[i]).html(text);
                $(lists[i]).parents("dl").hide();
                continue;
            }
            var newHtml = "";
            if (indexOf === 0) {
                newHtml = '<span class="highlight">' + val + '<\/span>' + text.substring(val.length);
            }
            if (indexOf > 0) {
                newHtml = text.substring(0, indexOf) + '<span class="highlight">' + val + '<\/span>' + text.substring(val.length + indexOf);
            }
            $(lists[i]).html(newHtml);
            $(lists[i]).parents("dl").show();
        }
        $(this).siblings(".worker-added-find-list").show();
        $(this).siblings(".worker-added-list").hide();

    });

    $(".worker-member").unbind().click("click", function () {
        var val = $(this).val();
        var isChecked = $(this).attr("checked");
        var box = $(this).parents(".worker-added-box");
        box.find("[value='" + val + "']").attr("checked", isChecked || false);
        totalWorker(box);
    });
    $(".worker-department").unbind().click("click", function () {
        var ulbox = $(this).siblings("ul");
        if (ulbox.length === 0) return true;
        var isChecked = $(this).attr("checked");
        var box = $(this).parents(".worker-added-box");
        var members = ulbox.find(".worker-member");
        for (var i = 0; i < members.length; i++) {
            var val = $(members[i]).val();
            box.find("[value='" + val + "']").attr("checked", isChecked || false);
        }
        totalWorker(box);

    });

    $(".worker-confirm").unbind().bind("click", function () {
        var $selected = $(this).parent(".worker-selected").siblings(".worker-added-find-list").find(":checked");

        var userids = [];

        var nums = 0;

        var html = "";

        for (var i = 0; i < $selected.length; i++) {
            var $show = $($selected[i]).attr("data-show");
            var val = $($selected[i]).val();
            if ($.inArray(val, userids) > -1) continue;
            nums++;
            html += '<div class="worker-added-item">' +
                '<span class="worker-added-item-result">' + $show + '<\/span>' +
                '<span class="worker-added-item-remove" onclick="removeWorkerItem.call(this,\'' + val + '\')">&times;<\/span><\/div>';

            userids.push(val);
        }

        var max = $(this).attr("data-max");

        if (max < nums) {
            layer.msg("最大允许选择" + max + "人");
            return;
        }
        var $added = $(this).parents(".worker-added-box").siblings(".worker-added");
        $added.siblings(".worker-added-item").remove();
        $added.before(html);
        $added.siblings(".worker-added-box").slideUp();


    })

}

function totalWorker(box) {
    var selecteds = box.find(".worker-added-find-list :checked");

    var userids = [];
    var nums = 0;
    for (var i = 0; i < selecteds.length; i++) {
        var val = $(selecteds[i]).val()
        if ($.inArray(val, userids) > -1) continue;
        nums++;
        userids.push(val);
    }
    box.find(".worker-selected-num").text(nums);
    var departments = box.find(".worker-department");
    for (var i = 0; i < departments.length; i++) {
        var ulbox = $(departments[i]).siblings("ul");
        if (ulbox.length === 0) continue;
        var memberNum = ulbox.find(".worker-member").length;
        if (memberNum === 0) continue;
        var checkedNum = ulbox.find(".worker-member:checked").length;
        $(departments[i]).attr("checked", memberNum === checkedNum);
    }
}

function removeWorkerItem(val) {
    var workerItem = $(this).parent();
    var box = workerItem.parent();
    box.find("[value='" + val + "']").attr("checked", false);
    workerItem.remove();
    totalWorker(box);
}

function initInputFind() {
    var html = [
        '<div class="input-auto-find-text">',
        '<span class="input-auto-find-text-result"></span>',
        '<span class="input-auto-find-remove" onclick="removeFindItem.call(this)">×</span>',
        '</div>'
    ].join("");
    var box = [
        '<div class="input-auto-find-box"></div>',
    ].join("");
    var htmlList = '<div class="input-auto-find-text-list"><\/div>';
    $(".input-auto-find").each(function () {
        if ($(this).find(".input-auto-find-text").length === 0) $(this).append(html);
        if ($(this).find(".input-auto-find-box").length === 0) $(this).append(box);
        $(this).find(".input-auto-find-remove").unbind().bind("click", function () {
            var box = $(this).parent();
            box.hide();
            box.siblings("input[type='text']").attr("readonly", false).val("").show();
        });
        $(".input-auto-find input[type='text']").unbind().bind("keyup", function (event) {
            var val = $(this).val();
            var box = $(this).siblings(".input-auto-find-box");
            if (val) {
                var groupName = $(this).parent().attr("data-find-group");
                var excepts = [];
                if (groupName) {
                    var groupList = $("[data-find-group='" + groupName + "'] input[type='hidden']");
                    for (var i = 0; i < groupList.length; i++) {
                        var exceptVal = $(groupList[i]).val();
                        if (exceptVal) excepts.push(exceptVal);
                    }
                }
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data: {
                        keywords: val,
                        except: excepts.join(",")
                    },
                    url: $(this).attr("data-url"),
                    success: function (res) {
                        var html = "";
                        for (var i in res) {
                            html += [
                                '<div class="input-auto-find-item" data-id="' + res[i][0] + '" data-text="' + res[i][1] + '">',
                                res[i][2],
                                '</div>'
                            ].join("");
                        }
                        if (html) {
                            box.html(html);
                            box.show();
                            box.find(".input-auto-find-item").unbind().bind("click", function () {
                                var dataId = $(this).attr("data-id");
                                var dataText = $(this).attr("data-text");
                                box.hide();
                                box.siblings("input[type='hidden']").val(dataId);
                                box.siblings("input[type='text']").attr("readonly", true).val("").hide();
                                box.siblings(".input-auto-find-text").children(".input-auto-find-text-result").html(dataText);
                                box.siblings(".input-auto-find-text").show();
                            });
                        }
                        return false;
                    }, error: function () {
                        return false;
                    }
                });
            } else {
                $(this).siblings(".input-auto-find-box").hide();
            }
        });
    });
    $(".input-auto-find-multi").each(function () {
        if ($(this).find(".input-auto-find-text-list").length === 0) $(this).prepend(htmlList);
        if ($(this).find(".input-auto-find-box").length === 0) $(this).append(box);
        $(".input-auto-find-multi input[type='text']").unbind().bind("keyup", function () {
            var val = $(this).val();
            var box = $(this).siblings(".input-auto-find-box");
            if (val) {
                var groupName = $(this).parent().attr("data-find-group");
                var excepts = [];
                if (groupName) {
                    var groupList = $("[data-find-group='" + groupName + "'] input[type='hidden']");
                    for (var i = 0; i < groupList.length; i++) {
                        var exceptVal = $(groupList[i]).val();
                        if (exceptVal) excepts.push(exceptVal);
                    }
                } else {
                    var exceptVal = box.siblings("input[type='hidden']").val();
                    if (exceptVal) excepts.push(exceptVal);
                }
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data: {
                        keywords: val,
                        except: excepts.join(",")
                    },
                    url: $(this).attr("data-url"),
                    success: function (res) {
                        var html = "";
                        for (var i in res) {
                            html += [
                                '<div class="input-auto-find-item" data-id="' + res[i][0] + '" data-text="' + res[i][1] + '">',
                                res[i][2],
                                '</div>'
                            ].join("");
                        }
                        if (html) {
                            box.html(html);
                            box.find(".input-auto-find-item").click(function () {
                                var dataId = $(this).attr("data-id");
                                var dataText = $(this).attr("data-text");
                                box.hide();
                                var html = [
                                    '<div class="input-auto-find-text" data-id="' + dataId + '">',
                                    '<span class="input-auto-find-text-result">' + dataText + '</span>',
                                    '<span class="input-auto-find-remove" onclick="removeFindItem.call(this)">×</span>',
                                    '</div>'
                                ].join("");

                                box.siblings(".input-auto-find-text-list").append(html);
                                box.siblings(".input-auto-find-text-list").show();
                                box.siblings(".input-auto-find-text").addClass("display");
                                var items = box.siblings(".input-auto-find-text-list").find(".input-auto-find-text");

                                var datas = [];

                                for (var i = 0; i < items.length; i++) {
                                    datas.push($(items[i]).attr("data-id"));
                                }
                                box.siblings("input[type='hidden']").val(datas.join(","));
                                box.siblings("input[type='text']").val("");
                            });
                            box.show();
                        }
                        return false;
                    }, error: function () {
                        return false;
                    }
                });
            } else {
                box.hide();
            }
        });
    });
}

function setCookie(name, value) {
    var Days = 30;
    var exp = new Date();
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString() + ";path=/";
}

function getCookie(name) {
    var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");

    if (arr = document.cookie.match(reg))

        return unescape(arr[2]);
    else
        return null;
}

function delCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval = getCookie(name);
    if (cval != null)
        document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString() + ";path=/";
}


$(function () {

    $(".menu-open-close").click(function () {
        var $menuOpenClose = getCookie("menuOpenClose");
        if ($menuOpenClose) delCookie("menuOpenClose");
        else setCookie("menuOpenClose", "true");
        location.reload(true);
    });
    $(".index-header,.index-main").live("click", function (event) {
        if ($('#contentedits').hasClass("contentedits-show")) {
            $('#contentedits').addClass('contentedits-hide-animate');
            setTimeout(function () {
                $('#contentedits').removeClass('contentedits-show');
                $('#contentedits').removeClass('contentedits-hide-animate');
            }, 500)
        }
    });

    $("body").click(function (e) {
        if (typeof e.srcElement === "undefined") return;
        var curElement = $(e.srcElement);
        do {
            if (curElement.attr("class") === "userinfo") return;
            curElement = curElement.parent();
        } while (curElement && curElement[0] && curElement[0].tagName && curElement[0].tagName.toUpperCase() !== "BODY");
        $('ul.usermenu').css('display', 'none');
        $("#headicon").removeClass('usermenu-hover');
    });
    // $("body").click(function (e) {
    //     if ($("#menudiv").hasClass("menu-2")) return;
    //     if (typeof e.srcElement === "undefined") return;
    //     var curElement = $(e.srcElement);
    //     do {
    //         if (curElement.attr("id") === "menudiv") return;
    //         curElement = curElement.parent();
    //     } while (curElement && curElement[0] && curElement[0].tagName && curElement[0].tagName.toUpperCase() !== "BODY");
    //     $(".main-menu-li").removeClass('current');
    //     $(".sub-menu-2").hide();
    // });

    $("#contentedits,.image-preview,.layui-layer,.layui-layer-close").live("click", function (event) {
        event.stopPropagation();
    });

    $(".head-action").click(function (event) {
        event.stopPropagation();
    });

    if (window.history && window.history.pushState) {
        //$(window).on('popstate', function (e) {
        //    initMenu();
        //});
    }

    // $("body").bind("mouseover", function (e) {
    //     if (!$("#menudiv").hasClass("menu-2")) return;
    //     if (typeof e.srcElement === "undefined") return;
    //     var curElement = $(e.srcElement);
    //     do {
    //         if (curElement.attr("class") === "sub_menu_3_4") {
    //             $(".sub-menu-2").css("visibility", "hidden");
    //             e.stopPropagation();
    //             return;
    //         }
    //         if (curElement.attr("id") === "menudiv" || curElement.attr("class") === "menu_2_item") return;
    //         curElement = curElement.parent();
    //     } while (curElement && curElement[0] && curElement[0].tagName && curElement[0].tagName.toUpperCase() !== "BODY");
    //     $(".sub-menu-2").css("visibility", "hidden");
    // });
    initMenu();
    $(".menu-2 a.menu_main_a").live("click", function (e) {
        var that = this;
        var $nextul = $(that).next("ul");
        $(".sub-menu-2").css("visibility", "hidden");
        $nextul.css("visibility", "visible");
        var parli = $(this).parent();
        parli.addClass("current");
        parli.siblings(".current").removeClass("current");


        if ($nextul.length > 0) {
            $("#contentdiv").addClass("content_show_3_4");
        } else {
            $("#contentdiv").removeClass("content_show_3_4");
        }
        var hrefs = $(this).attr("href");
        if (hrefs) {
            var selectedMenu = $nextul.find("a[href='" + hrefs + "']");
            $nextul.find("li.current").removeClass("current");
            if (selectedMenu.length > 0) {
                var $li = selectedMenu.parent().length > 0 ? selectedMenu.parent() : selectedMenu.parents("li");
                selectedMenu.parents("li").addClass("current").siblings(".current").removeClass("current");
                var sub_menu_3_4 = $li.find(".sub_menu_3_4");
                $(".sub_menu_3_4").addClass("hide");
                if (sub_menu_3_4.length > 0) {
                    $("#contentdiv").addClass("content_show_3_4");
                    sub_menu_3_4.removeClass("hide");
                }
            }
        }
        e.stopPropagation();
    });

    $(".menu:not(.menu-2) a.menu_main_a").live("click", function (e) {
        var $nextul = $(this).next("ul");
        $nextul.slideToggle(100);
        var parli = $(this).parent();
        parli.toggleClass("current");
        if (parli.hasClass("current")) {
            parli.siblings(".current").removeClass("current").children("ul").slideUp(100);
        }
        $("#contentdiv").removeClass("content_show_3_4");
    });
    $(".menu.menu-2 a.menu_2_a").live("click", function (e) {
        var parli = $(this).parent();
        parli.addClass("current");
        $(".sub_menu_3_4 .current").removeClass("current");
        parli.siblings(".current").removeClass("current");
    });

    $(".menu-2 a.menu_3_a").live("click", function () {
        $(".menu-4 .current").removeClass("current");
        $(".sub_menu_3_4 .current").removeClass("current");
        var parli = $(this).parent("li");
        parli.addClass("current");
        parli.siblings(".current").removeClass("current");
        parli.parents("li.menu_2_item").addClass("current").siblings(".current").removeClass("current");
        if ($(this).attr("href") == "##") {
            var $menu_4_a = $(this).siblings(".menu-4").find(".menu_4_a");
            if ($menu_4_a.length > 0) {
                $menu_4_a.first().click();
            }
        }
    });


    $(".menu:not(.menu-2) a.menu_2_a").live("click", function () {
        var parli = $(this).parent("li");
        parli.addClass("current");
        parli.siblings(".current").removeClass("current");
        $(".sub_menu_3_4").addClass("hide");
        var sub_menu_3_4 = $(this).siblings(".sub_menu_3_4");
        if (sub_menu_3_4.length > 0) {
            $(this).siblings(".sub_menu_3_4").removeClass("hide");
            $("#contentdiv").addClass("content_show_3_4");
        } else {
            $("#contentdiv").removeClass("content_show_3_4");
        }

        if ($(this).attr("href") == "##") {
            var $menu_4_a = $(this).siblings(".sub_menu_3_4").find(".menu_4_a");
            if ($menu_4_a.length > 0) {
                var active_menu_4_a = $(this).siblings(".sub_menu_3_4").find(".menu-4 .current .menu_4_a");
                if (active_menu_4_a.length > 0) {
                    active_menu_4_a.first().trigger("click");
                    return false;
                }
                var active_menu_3_a = $(this).siblings(".sub_menu_3_4").find(".current .menu-4 .menu_4_a");

                if (active_menu_3_a.length > 0) {
                    active_menu_3_a.first().trigger("click");
                    return false;
                }
                $menu_4_a.first().click();
            } else {
                var $menu_3_a = $(this).siblings(".sub_menu_3_4").find(".menu_3_a");
                var active_menu_3_a = $(this).siblings(".sub_menu_3_4").find(".current .menu_3_a");
                if (active_menu_3_a.length > 0) {
                    active_menu_3_a.first().trigger("click");
                    return false;
                }
                $menu_3_a.first().click();
            }
        }
    });
    $(".menu:not(.menu-2) a.menu_3_a").live("click", function () {
        $(".menu-4 .current").removeClass("current")
        var parli = $(this).parent("li");
        parli.addClass("current");
        parli.siblings(".current").removeClass("current");

        if ($(this).attr("href") == "##") {
            var $menu_4_a = $(this).siblings(".menu-4").find(".menu_4_a");
            if ($menu_4_a.length > 0) {
                $menu_4_a.first().click();
            }
        }

    });
    $("a.menu_4_a").live("click", function () {
        $(".menu-4 .current").removeClass("current");
        var parli = $(this).parent("li");
        parli.addClass("current");
        parli.parent("ul").parent("li").addClass("current").siblings(".current").removeClass("current");
    });

    $("a[href]").live("click", function () {
        var targetSet = $(this).attr("target");
        if (targetSet && targetSet == "_blank") return true;
        var clickAction = $(this).attr("data-click");
        if (clickAction) {
            if (typeof (window[clickAction]) == "function") window[clickAction].call(this);

            return false;
        }
        var menu_id = $(this).attr("data-menu-id");
        if (menu_id) setUrl("menu_id", menu_id);
        var url = $(this).attr("href");
        if (url) {
            var arr = url.match(/#([1-9]+)$/);
            if (arr) {
                changeMenu(arr[1]);
            }
        }
        if (typeof($(this).attr("data-side-form")) != "undefined" || typeof($(this).attr("side-form")) != "undefined") {
            var oldUrl = $("#contentedits").attr("data-old-url") || "";

            var sideWidth = $(this).attr("data-side-form") || $(this).attr("data-side-form");
            $("#contentedits").css("width", sideWidth || 800);

            if (!$("#contentedits").hasClass("contentedits-show")) $("#contentedits").addClass('contentedits-show');

            if (oldUrl && oldUrl === url) return false;

            $.ajax({
                type: "GET",
                dataType: "html",
                headers: {
                    ajaxExtraMethod: "SideForm"
                },
                url: url,
                beforeSend: function () {
                    init.clearReady();
                    $("#contenteditsdiv").html("");
                    $("#contenteditslodingpercentage").animate({
                        width: "20%",
                    }, 100);
                },
                complete: function (request) {
                    $("#contenteditslodingpercentage").animate({
                        width: "100%",
                    }, 300, function () {
                        $("#contenteditslodingpercentage").css("width", 0);
                    });
                    $("#contenteditsdiv").html(request.responseText).load(function () {
                    });
                    if (request.status === 200 && !(/content-method: dump/.test(request.getAllResponseHeaders()))) {
                        $("#contentedits").attr("data-old-url", url);
                    } else {
                        $("#contentedits").attr("data-old-url", "");
                    }
                    init.commit();
                }
            });
            return false;
        }
        hideSideForm();
        if (typeof($(this).attr("data-confirm")) != "undefined" || typeof($(this).attr("confirm")) != "undefined") {
            var confirmTile = $(this).attr("data-confirm") || $(this).attr("confirm");
            layer.confirm(confirmTile, function () {
                if (url == "##") return false;
                if (/^javascript\:/.test(url)) return false;
                var toUrl = setUrl("url", url);
                getPage(toUrl);
            });
            return false;
        }


        if (typeof($(this).attr("data-popup")) != "undefined" || typeof($(this).attr("popup")) != "undefined") {
            var popupInfo = $(this).attr("data-popup") || $(this).attr("popup");
            var popupParams = popupInfo.split(/\,(?!\,)/);
            var defaultParams = ["弹出窗口", "720px", "600px"];
            if (popupParams.length == 0) {
                popupParams = defaultParams;
            } else if (popupParams.length == 1) {
                popupParams = [popupParams[0] || defaultParams[0], defaultParams[1], defaultParams[2]];
            } else if (popupParams.length == 2) {
                popupParams = [popupParams[0] || defaultParams[0], popupParams[1] || defaultParams[1], defaultParams[2]];
            }
            if (!popupParams[2]) popupParams[2] = defaultParams[2];
            $.ajax({
                type: "GET",
                dataType: "html",
                url: url,
                complete: function (request) {
                    layer.open({
                        title: popupParams[0], type: 1, closeBtn: 1, shadeClose: true, shade: 0.3,
                        skin: 'layui-layer-rim', //加上边框
                        area: [popupParams[1], popupParams[2]], //宽高
                        content: request.responseText //这里content是一个普通的String
                    });
                    return false;
                }, error: function () {
                    return false;
                }
            });
            return false;
        }
        if (typeof($(this).attr("data-ifr")) != "undefined" || typeof($(this).attr("ifr")) != "undefined") {
            var ifrInfo = $(this).attr("data-popup") || $(this).attr("popup");
            var ifrParams = ifrInfo.split(/\,(?!\,)/);
            var defaultIfrParams = ["弹出窗口", "720px", "600px"];
            if (ifrParams.length == 0) {
                ifrParams = defaultIfrParams;
            } else if (ifrParams.length == 1) {
                ifrParams = [ifrParams[0] || defaultIfrParams[0], defaultIfrParams[1], defaultIfrParams[2]];
            } else if (ifrParams.length == 2) {
                ifrParams = [ifrParams[0] || defaultIfrParams[0], ifrParams[1] || defaultIfrParams[1], defaultIfrParams[2]];
            }
            if (!ifrParams[2]) ifrParams[2] = defaultIfrParams[2];
            layer.open({
                title: ifrParams[0], type: 1, closeBtn: 1, shadeClose: true, shade: 0.3,
                skin: 'layui-layer-rim', //加上边框
                area: [ifrParams[1], ifrParams[2]], //宽高
                content: url //这里content是一个普通的String
            });
            return false;
        }

        if (/^javascript\:/.test(url)) return false;
        if (url == "##") return false;
        var fromUrl = location.hash ? location.hash.substring(1) : "";
        var toUrl = setUrl("url", url);
        console.log(toUrl);
        getPage(toUrl, null, null, fromUrl);
        return false;
    });

    $(".stdtable tr").live("mouseover", function () {
        $(this).addClass('curr');
    });
    $(".stdtable tr").live("mouseout", function () {
        $(this).removeClass('curr');
    });
    $(".stdtable tr").live("click", function () {
        $(this).addClass('selected').siblings(".selected").removeClass("selected");
    });


    /* usermemu */
    $('.userinfo').mouseover(function () {
        $('ul.usermenu').css('display', 'inline-block');
        $('.userinfo div#headicon').addClass("usermenu-hover");//css('background-color', '#4582cb');
    });
    $('#headicon').click(function (e) {
        if ($(this).hasClass("usermenu-hover")) {
            $('ul.usermenu').css('display', 'none');
            $(this).removeClass('usermenu-hover');
        } else {
            $('ul.usermenu').css('display', 'inline-block');
            $(this).addClass("usermenu-hover");//css('background-color', '#4582cb');
        }
    });
    $(".usermenu li").click(function () {
        setTimeout(function () {
            $('ul.usermenu').css('display', 'none');
            $("#headicon").removeClass('usermenu-hover');
        }, 300)
    });
    $('.siteinfo div#siteicon,ul.sitemenu').mouseover(function () {
        $('ul.sitemenu').css('display', 'inline-block');
        $('.siteinfo div#siteicon').addClass("sitemenu-hover");
    });
    $('.siteinfo div#siteicon,ul.sitemenu').mouseout(function () {
        $('ul.sitemenu').css('display', 'none');
        $('.siteinfo div#siteicon').removeClass('sitemenu-hover');//.css('background-color', '');
    });

    $('.right-menu-item').hover(function () {
        $(this).addClass("right-menu-item-hover");//css('background-color', '#4582cb');
    }, function () {
        $(this).removeClass('right-menu-item-hover');
    });

    $(".navtab>.navtab-item").live("click", function () {
        var targetClass = $(this).parent().attr("data-target");
        $("." + targetClass).hide();
        var targetId = $(this).attr("data-target");
        $("#" + targetId).show();
        $(this).addClass("navtab-item-selected").siblings(".navtab-item-selected").removeClass("navtab-item-selected");
    });

    $(".more-action>.arrows").live("click", function (e) {
        var $parentObj = $(this).parent();
        $parentObj.toggleClass("open");
        var parentObj = $parentObj[0];
        var moreActions = $(".more-action");
        for (var i = 0; i < moreActions.length; i++) {
            if (moreActions[i] === parentObj) continue;
            $(moreActions[i]).removeClass("open");
        }
        e.stopPropagation();
    });

    $(".stdtable a,.stdtable input").live("click", function (e) {
        e.stopPropagation();
    });

    $(".check-all").live("click", function () {
        $("." + $(this).attr("data-class")).attr("checked", $(this).attr("checked") ? true : false);
    });
    $("input[class][type='checkbox']").live("click", function () {
        $(".check-all[data-class]").each(function () {
            var dataClass = $(this).attr("data-class");
            var checkedLength = $("." + dataClass + ":checked").length;
            if (checkedLength > 0) {
                $(this).parents(".list-more-action").addClass("active");
            } else {
                $(this).parents(".list-more-action").removeClass("active");
            }
            $(this).attr("checked", $("." + dataClass).length == checkedLength ? true : false);
        });
    });

    $(".list-more-action-item").live("click", function () {
        var dataClass = $(this).parent().find(".check-all").attr("data-class");
        var checkedLength = $("." + dataClass + ":checked").length;
        var confirmTitle = $(this).attr("data-conrifm") || "确定将选中的#num#条记录执行" + $(this).text() + "操作？";
        confirmTitle = confirmTitle.replace("#num#", checkedLength);
        var $this = $(this);
        layer.confirm(confirmTitle, function () {
            init.clearReady();
            $.ajax({
                type: "POST",
                dataType: "html",
                data: $this.parents("form").serialize(),
                url: $this.attr("data-url"),
                complete: function (request) {
                    var msgIndex = layer.msg("正在处理，请稍候", {
                        offset: 't',
                        time: 0,
                        icon: 16,
                        shade: 0.1
                    });
                    layer.close(msgIndex);
                    $("#contentdiv").html(request.responseText);
                    init.commit();
                }
            });
        })

    });
});
KindEditor.ready(function () {
    KindEditor.lang({
        imageupload: "上传图片",
        filesupload: "上传文件",
    });
    KindEditor.plugin("imageupload", function (K) {
        var self = this, name = 'imageupload';
        self.clickToolbar(name, function () {
            var menu = self.createMenu({
                name: name,
                width: 150
            });
            menu.addItem({
                title: '本地上传',
                click: function () {
                    uploader.kindFilePicker(self);
                }
            });
            menu.addItem({
                title: "图片库",
                click: function () {
                    uploader.showKindStock(self);
                }
            });

        });
    });
    KindEditor.plugin("filesupload", function (K) {
        var self = this, name = 'filesupload';
        self.clickToolbar(name, function () {
            var menu = self.createMenu({
                name: name,
                width: 150
            });
            menu.addItem({
                title: '本地上传',
                click: function () {
                    uploader.kindFilesPicker(self);
                }
            });
            menu.addItem({
                title: "文件库",
                click: function () {
                    uploader.showKindFileStock(self);
                }
            });

        });
    });

});

function countDown(endTimes) {

    if (!endTimes) return 0;
    endTimes = endTimes.replace(/\-+/g, ":");
    endTimes = endTimes.replace(/\s+/g, ":");
    var endTimesArr = endTimes.split(/:/g);
    var endTimeDate = new Date(endTimesArr[0], endTimesArr[1] - 1, endTimesArr[2], endTimesArr[3], endTimesArr[4], endTimesArr[5]);
    var times = endTimeDate.getTime() - (new Date().getTime());

    if (isNaN(times)) {
        return "";
    }
    var $return = "";

    if (times < 0) {
        $return = "已超时:";
        times = Math.abs(times);
    } else if (times === 0) {
        return "已超时";
    }
    var day = Math.floor(times / 1000 / 60 / 60 / 24);
    var hour = Math.floor(times / 1000 / 60 / 60 % 24);
    var minute = Math.floor(times / 1000 / 60 % 60);
    var second = Math.floor(times / 1000 % 60);

    if (day > 0) $return += day + "天";
    if (hour > 0) $return += hour + "小时";
    if (minute > 0) $return += minute + "分";
    return $return + second + "秒";
}