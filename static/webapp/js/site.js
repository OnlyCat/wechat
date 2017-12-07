$("body").height(window.innerHeight);
$(window).ready(function(){
    setTimeout( function(){
        $(".loading").fadeOut("slow",function(){
            $(this).remove();
            $(".header").animate({top:"-2.6rem"}, "slow");
            $(".footer").animate({bottom:"-3.2rem"}, "slow");
        });
    }, 3000);
    var currentScrollPosition = 0;
    var historyScrollPosition = 0;
    $(".content").scroll(function(event) {
        currentScrollPosition = $(this).scrollTop();
        if(historyScrollPosition <= currentScrollPosition){//下滚
            $(".header").stop().animate({top:"0rem"});
            $(".footer").stop().animate({bottom:"-3.2rem"});
        } else{//上滚
            $(".header").stop().animate({top:"-2.6rem"});
            $(".footer").stop().animate({bottom:"0rem"});
        }
       historyScrollPosition = currentScrollPosition;
    });

    $(".content").click(function() {
        $(".header").stop().animate({top:"-2.6rem"});
        $(".footer").stop().animate({bottom:"-3.2rem"});
    });

    $(".content").dblclick(function() {
        $(".header").stop().animate({top:"0rem"});
        $(".footer").stop().animate({bottom:"0rem"});
    });

    $(".f-menu ul li").click(function () {
        $(".f-menu ul li").removeClass("active");
        $(this).addClass("active");
    })
});