var myScroll;
function loadIScroll() {

    var pullDownEl, pullDownOffset,
            pullUpEl, pullUpOffset, myScrollPage = 1;
    pullDownEl = document.getElementById('pullDown');
    pullDownOffset = pullDownEl.offsetHeight;
    pullUpEl = document.getElementById('pullUp');
    pullUpOffset = pullUpEl.offsetHeight;


    $(pullUpEl).click(function () {
        pullUpEl.querySelector('.pullUpLabel').innerHTML = '正在加载...';
        pullUpAction();
    });


    /**
     * 下拉刷新 （自定义实现此方法）
     * myScroll.refresh();		// 数据加载完成后，调用界面更新方法
     */
    function pullDownAction() {
        $.ajax({
            type: "get",
            url: $(pullDownEl).attr("data-url"),
            data: {page: 0},
            dataType: "json", error: function () {
                pullDownEl.className = "iscroll-pullDown noicon";
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '网络异常';
                setTimeout(function () {
                    myScroll.refresh();
                }, 200);
            }}).then(function (data) {
            if (data.status == "y") {
                addItem(data.data, "down");
                myScrollPage = 1;
            } else {
                setTimeout(function () {
                    pullDownEl.className = "iscroll-pullDown noicon";
                    pullDownEl.querySelector('.pullDownLabel').innerHTML = data.message;
                }, 200);
            }
            setTimeout(function () {
                myScroll.refresh();
            }, 200);
        });
    }

    /**
     * 滚动翻页 （自定义实现此方法）
     * myScroll.refresh();数据加载完成后，调用界面更新方法	
     */
    function pullUpAction() {
        $.ajax({
            type: "get",
            url: $(pullUpEl).attr("data-url"),
            data: {page: myScrollPage},
            dataType: "json", error: function () {
                setTimeout(function () {
                    pullUpEl.className = "iscroll-pullUp noicon";
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = '网络异常';
                }, 200);
                myScroll.refresh();
            }}).then(function (data) {
            if (data.status == "y") {
                addItem(data.data, "up");
                myScrollPage++;
                pullUpEl.className = 'iscroll-pullUp';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '加载更多';
            } else {
                setTimeout(function () {
                    pullUpEl.className = "iscroll-pullUp noicon";
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = '暂无更多';
                }, 200);
            }
            setTimeout(function () {
                myScroll.refresh();
            }, 200);
        });
    }
    myScroll = new iScroll('wrapper', {
        scrollbarClass: 'myScrollbar',
        useTransition: false,
        topOffset: pullDownOffset,
        onRefresh: function () {
            if (pullDownEl.className.match('loading')) {
                pullDownEl.className = '';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新';
            } else if (pullUpEl.className.match('loading')) {
                pullUpEl.className = '';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '加载更多';
            }
        },
        onScrollMove: function () {
            if (this.y > 5 && !pullDownEl.className.match('flip')) {
                pullDownEl.className = 'flip';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '正在刷新...';
                this.minScrollY = 0;
            } else if (this.y < 5 && pullDownEl.className.match('flip')) {
                pullDownEl.className = '';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新';
                this.minScrollY = -pullDownOffset;
            } else if (this.y < (this.maxScrollY - 5) && !pullUpEl.className.match('flip')) {
                pullUpEl.className = 'flip';
            } else if (this.y > (this.maxScrollY + 5) && pullUpEl.className.match('flip')) {
                pullUpEl.className = '';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '加载更多';
                this.maxScrollY = pullUpOffset;
            }
        },
        onScrollEnd: function () {
            if (pullDownEl.className.match('flip')) {
                pullDownEl.className = 'loading';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '正在刷新...';
                pullDownAction();	// Execute custom function (ajax call?)
            } else if (pullUpEl.className.match('flip')) {
                pullUpEl.className = 'loading';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '正在加载...';
                pullUpAction();
            }
        }
    });

    setTimeout(function () {
        document.getElementById('wrapper').style.left = '0';
    }, 800);
}

//初始化绑定iScroll控件 
document.addEventListener('touchmove', function (e) {
    e.preventDefault();
}
, false);
document.addEventListener('DOMContentLoaded', loadIScroll, false); 