window.init = {
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

function sideSuccessAndJump(message, toUrl, time) {
    layer.msg(message, {
        offset: 't',
        time: time * 1000,
        icon: 1
    });
    if (toUrl) location.replace(toUrl);
    else location.reload(true);
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

function initSideFrom(formClass, $url) {
    $("#contentedits .contenteditsSubmit").unbind().bind("click", function () {
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
                        layer.close(msgIndex);
                        $("#contenteditsdivRes").html(request.responseText);
                        $("#contentedits").attr("data-old-url", "")
                    }
                });
            }

        });
    });
};

$(function () {


    $("a[href]").live("click", function () {
        var url = $(this).attr("href");
        if (typeof($(this).attr("data-side-form")) != "undefined" || typeof($(this).attr("side-form")) != "undefined") {
            var oldUrl = $("#contentedits").attr("data-old-url") || "";

            var sideWidth = $(this).attr("data-side-form") || $(this).attr("data-side-form");
            $("#contentedits").css("width", sideWidth || 500);
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
                    $("#contentedits").attr("data-old-url", url);
                    init.commit();
                }
            });
            return false;
        }
    })
});