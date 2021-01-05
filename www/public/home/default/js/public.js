/**
 * Created by jeechange on 2019/6/6.
 */
$(function () {
    var bodyWidth = $('body').width();
    $(document).on('scroll', function () {
        var scroH = $(window).scrollTop();
        if (bodyWidth > 426) {
            // var _hmt = _hmt || [];
            // (function () {
            //     var hm = document.createElement("script");
            //     hm.src = "https://hm.baidu.com/hm.js?cefb86b9fd3bc98150aea311f0deba38";
            //     var s = document.getElementsByTagName("script")[0];
            //     s.parentNode.insertBefore(hm, s);
            // })();
            if (scroH > 260) {
                $('.nav_navigation').css({'position':'fixed','background':'rgba(40, 85, 156, 1)','z-index':'20'});
            }
            else {
                $('.nav_navigation').css({'position':'relative','background':'unset'});
            }
        }
        else {

        }
    });


    $('.menu').on('click touchstart', function () {
        $('.navs').stop().animate({'right':'0px'});
    });

    $(window).on('click touchstart', function () {
        var isShow = $('.navs').css('right');
        if (isShow != '0px') {
            // return false;
        }
        else {
            $('.navs').stop().animate({'right':'-60vw'});
        }
    });

    var scroH = 0;

    function bodyWidths() {
        var body = $('body').width();
        return body;
    }

    function pPadding() {
        var navHeight = $('.nav .nav_navigation').css('height');
        return navHeight;
    }

    $(document).on('scroll', function () {
        pPadding();
        scroH = $(window).scrollTop();
        var body = bodyWidths();
        if (body < 426) {
            if (scroH > 210) {
                $('.nav_navigation').addClass('hasBg');
                $('.icons a').attr('href','http://p.qiao.baidu.com/cps/chat?siteId=13229903&userId=10577487');
            }
            else {
                $('.nav_navigation').removeClass('hasBg');
                $('.icons a').attr('href','http://p.qiao.baidu.com/cps2/chatIndex?reqParam=%7B%22from%22%3A0%2C%22sid%22%3A%22-100%22%2C%22tid%22%3A%22-1%22%2C%22ttype%22%3A1%2C%22siteId%22%3A%2213227066%22%2C%22userId%22%3A%2210577487%22%2C%22pageId%22%3A0%7D');
            }
        }
        $('#position').text(scroH);

    })

    $('.inter ul li').on('click', function () {
        var url = $(this).find('a').attr('href');
        location = url;
    })

    $('.top').click(function () {
        $('html , body').animate({
            scrollTop: 0
        }, 'slow');
    })
})
var _hmt = _hmt || [];
// (function () {
//     var hm = document.createElement("script");
//     hm.src = "https://hm.baidu.com/hm.js?620c7d544d358618f1a44b00216624eb";
//     var s = document.getElementsByTagName("script")[0];
//     s.parentNode.insertBefore(hm, s);
// })();