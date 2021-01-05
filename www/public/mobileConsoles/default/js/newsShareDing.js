$(function(){

})
function newsCopy(message) {
    var input = document.createElement("input");
    input.value = message;
    document.body.appendChild(input);
    input.select();
    input.setSelectionRange(0, input.value.length), document.execCommand('Copy');
    document.body.removeChild(input);
    $.alert('复制成功');
}

function newsShareDing(content, url, corpId, title) {
    var that = this;
    dd.biz.chat.pickConversation({
        corpId: corpId, //企业id
        isConfirm: false, //是否弹出确认窗口，默认为true
        onSuccess: function (res) {
            dd.runtime.permission.requestAuthCode({
                corpId: corpId,
                onSuccess: function (result) {
                    $.showPreloader('正在分享，请稍候...');
                    $.ajax({
                        url: $(that).attr("data-url"),
                        type: "POST",
                        data: {
                            url: url,
                            cid: res.cid,
                            title: title,
                            content: content,
                            code: result.code
                        },
                        dataType: "json",
                        success: function (res) {
                            $.hidePreloader();
                            if (res.status === "n") {
                                $.toast(res.info);
                                return;
                            }
                            $.toast("分享成功");
                        }, error: function (xhr) {
                            alert(xhr.responseText)
                        }
                    });
                },
                onFail: function (err) {
                    $(".show-message").text(err.errorMessage).css("color", "red");
                }
            });
        },
        onFail: function (err) {
            $.alert(err.errorMessage);
        }
    });
}
