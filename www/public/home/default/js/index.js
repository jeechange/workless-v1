$(document).ready(function () {
    var count = 0;//计数
    function changes() {
        if ($(window).width() > 450) {
            switch (count) {
                case 0:
                    $('.theEnd img').eq(0).stop().animate({'opacity':0},1000);
                    $('.theEnd img').eq(1).stop().animate({'opacity':1},1000);
                    $('.maskFirst').css('animation', 'mf01 1s ease-in-out 0s both');
                    $('.maskSecond').css('animation', 'ms01 1s ease 0.35s both');
                    count++;
                    break;
                case 1:
                    $('.theEnd img').eq(1).stop().animate({'opacity':0},1000);
                    $('.theEnd img').eq(2).stop().animate({'opacity':1},1000);
                    $('.maskFirst').css('animation', 'mf02 1s ease 0s both');
                    $('.maskSecond').css('animation', 'ms02 1s ease 0.35s both');
                    count++;
                    break;
                case 2:
                    $('.theEnd img').eq(2).stop().animate({'opacity':0},1000);
                    $('.theEnd img').eq(0).stop().animate({'opacity':1},1000);
                    $('.maskFirst').css('animation', 'mf03 1s ease 0s both');
                    $('.maskSecond').css('animation', 'ms03 1s ease 0.35s both');
                    count = 0;
                    break;
            }
        }
    }
    setInterval(changes,5000);

    /*右边导航栏*/
    $('.menu').on("click touchstart", function () {
        $('.rightNav').animate({'right': '0px'});
    });
    $(document).off().on("click touchstart", function () {
        var isShow = $('.rightNav').css('right');
        if (isShow != '0px') {
        } else {
            $('.rightNav').stop().animate({'right': '-60vw'});
        }
    });




    var scroH = 0;

    function bodyWidth(){
        var body = $('body').width();
        return body;
    }
    //
    // function pPadding() {
    //     var navHeight = $('.nav').css('height');
    //     console.log(navHeight);
    // }

    $(document).on('scroll', function () {
        // pPadding();
        scroH = $(window).scrollTop();
        var body = bodyWidth();
        if (body<426){
            $('.icons a').attr('href','http://p.qiao.baidu.com/cps/chat?siteId=13229903&userId=10577487');
            var _hmt = _hmt || [];
            // (function() {
            //     var hm = document.createElement("script");
            //     hm.src = "https://hm.baidu.com/hm.js?620c7d544d358618f1a44b00216624eb";
            //     var s = document.getElementsByTagName("script")[0];
            //     s.parentNode.insertBefore(hm, s);
            // })();
            if (scroH>1){
                $('.nav').css({'position':'fixed','background':'rgba(40, 85, 156, 1)','left':'0','top':'0','width':'100%','padding':'21px 0','opacity':'0.9'});
                $('.nav>a').css('margin-top',0);
                $('.menu').css('position','fixed');
            }
            else{
                $('.nav>a').css({'display': 'block', 'width': '93px', 'margin-top': '21px', 'margin-left': '10px'});
                $('.nav').css({'position':'unset','background':'unset','padding':'0'});
                $('.menu').css('position','absolute');
            }
        }
        else{
            $('.icons a').attr('href','http://p.qiao.baidu.com/cps2/chatIndex?reqParam=%7B%22from%22%3A0%2C%22sid%22%3A%22-100%22%2C%22tid%22%3A%22-1%22%2C%22ttype%22%3A1%2C%22siteId%22%3A%2213227066%22%2C%22userId%22%3A%2210577487%22%2C%22pageId%22%3A0%7D');
            var _hmt = _hmt || [];
            (function() {
                var hm = document.createElement("script");
                hm.src = "https://hm.baidu.com/hm.js?cefb86b9fd3bc98150aea311f0deba38";
                var s = document.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(hm, s);
            })();
            if (scroH>=1099){
                $('.nav').css({'background':'#28559C','position':'fixed'});
            }
            else{
                $('.nav').css({'background':'unset','position':'relative'});
            }
        }
        $('#position').text(scroH);
    });

    $('.nav li').on('click',function(){
        if (bodyWidth()<426) {
            var url = $(this).find('a').attr('href');
            location = url;
        }
    })


});

$(function () {
    $('.top').click(function () {
        $('html , body').animate({
            scrollTop: 0
        }, 'slow');
    })
})