$(document).ready(function(){
   function bodyWidth() {
       var body = $('body').width();
       if (body<426){
           return 'phone';
       }
       else{
           return 'pc'
       }
   }

   $('.content ul li').hover(function(){
       if (bodyWidth()!='phone'){
           $('.content ol p').css('display','block');
       }
   });
   $(window).scroll(function(){
       if (bodyWidth()!='phone'){
           $('.content ol p').css('display','block');
       }
   });

    $('.phoneNav ul li').on('click',function(){
        var index = $(this).index();
        $(this).parent().find('li').removeClass('on');
        $(this).addClass('on');
        $('.content ul li').removeClass('on');
        $('.content ul li').eq(index).addClass('on');
        var li_length = $('.content ol li').length;
        for (var as =0;as<li_length;as++){
            $('.content ol li').eq(as).find('.conts p').css('display','none');
            $('.content ol li').eq(as).find('.conts p').eq(index).css('display','block');
        }
    })
});