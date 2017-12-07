;(function($){

    var xcarousel = function(el, userConfig) {
        var _this = this
        this.el = el
        // 参数配置
        this.userConfig = userConfig

        this.config = {
            w: this.el.width(),
            current: 0,
            speed: 500,
            intervalTime: 5000
        }
        if(userConfig != null) {
            $.extend(this.config,this.userConfig);
        }


        // 保存查找dom元素
        var carousel_img = this.el.children('.carousel-img')
        var carousel_img_ul = carousel_img.children('ul')
        var carousel_img_ul_li = carousel_img_ul.children('li')

        // 初始化左右按钮
        this.el.append('<a href="javascript:" class="carousel-btn carousel-btn-left">&lt;</a>')
        this.el.append('<a href="javascript:" class="carousel-btn carousel-btn-right">&gt;</a>')
        var carousel_btn_left = this.el.children('.carousel-btn-left')
        var carousel_btn_right = this.el.children('.carousel-btn-right')

        // 初始化圆点
        this.el.append('<div class="carousel-dot"><ul></ul></div>')
        var carousel_dot = this.el.children('.carousel-dot')
        var carousel_dot_ul = carousel_dot.children('ul')
        var carousel_img_length = carousel_img_ul_li.length
        for (var i = 0; i < carousel_img_length; i++) {
            if(i === this.config.current){
                carousel_dot_ul.append('<li class="active"></li>')
            } else {
                carousel_dot_ul.append('<li></li>')
            }
        }
        var carousel_dot_ul_li = carousel_dot_ul.children('li')

        var img_width = $(".carousel").width();
        $(".carousel-img-ul li img").width(img_width)
        $(".carousel-img").width(carousel_img_length * img_width)

        //初始化图片
        $(window).resize(function() {
            var img_width = $(".carousel").width();
            $(".carousel-img-ul li img").width(img_width)
            $(".carousel-img").width(carousel_img_length * img_width)
        });

        // 初始化默认显示图片位置
        carousel_img_ul.css('left', - this.config.w * this.config.current)

        // 圆点切换点亮
        var active = function(i) {
            carousel_dot_ul_li.removeClass('active')
            carousel_dot_ul_li.eq(i).addClass('active')
        }

        // 右点击事件
        carousel_btn_right.on('click', function(event) {
            event.preventDefault()
            if(_this.config.current < carousel_img_length - 2){
                toggleInterval ()
                _this.config.current ++
                if(_this.config.current != carousel_img_length - 2) {
                    carousel_img_ul.stop(true, false).animate({left: - _this.config.w * _this.config.current - _this.config.w}, _this.config.speed, function () {
                        active(_this.config.current)
                    })
                }
                if (_this.config.current === carousel_img_length - 2) {
                    carousel_img_ul.stop(true, false).animate({left: - _this.config.w * _this.config.current - _this.config.w}, _this.config.speed, function() {
                        carousel_img_ul.css('left', - _this.config.w)
                        _this.config.current = 0
                        active(_this.config.current)
                    })
                }
            }
        })

        // 左点击事件
        carousel_btn_left.on('click', function(event) {
            event.preventDefault()
            if(_this.config.current > -1){
                toggleInterval ()
                _this.config.current --
                if(_this.config.current != -1){
                    carousel_img_ul.stop(true, false).animate({left: - _this.config.w * _this.config.current - _this.config.w}, _this.config.speed, function() {
                        active(_this.config.current)
                    })
                }
                if(_this.config.current === -1){
                    carousel_img_ul.stop(true, false).animate({left: 0}, _this.config.speed, function() {
                        carousel_img_ul.css('left', - _this.config.w * (carousel_img_length - 2))
                        _this.config.current = carousel_img_length - 3
                        active(_this.config.current)
                    })
                }
            }
        })

        //左滑动
        $(".carousel-img").on("touchstart", function(e) {
            e.preventDefault();
            startX = e.originalEvent.changedTouches[0].pageX,
            startY = e.originalEvent.changedTouches[0].pageY;
        });

        //右滑动
        $(".carousel-img").on("touchmove", function(e) {
            e.preventDefault();
            moveEndX = e.originalEvent.changedTouches[0].pageX,
            moveEndY = e.originalEvent.changedTouches[0].pageY,
            X = moveEndX - startX,
            Y = moveEndY - startY;
            if(X > 0){
                carousel_btn_left.click();
            } else if(X < 0){
                carousel_btn_right.click();
            }
        });


        // dot点击事件
        carousel_dot_ul_li.on('click', function(event) {
            event.preventDefault()
            toggleInterval ()
            var index = $(this).index()
            active(index)
            carousel_img_ul.stop(true, false).animate({left: - _this.config.w * index - _this.config.w}, _this.config.speed, function() {
                _this.config.current = index
            })
        })

        // 自动切换
        var carouselInt = setInterval(carouselInterval, _this.config.intervalTime)
        // 判断图片切换
        function carouselInterval() {
            if (_this.config.current < carousel_img_length - 2) {
                _this.config.current ++
                carousel_img_ul.stop(true, false).animate({left: - _this.config.w * _this.config.current - _this.config.w}, _this.config.speed, function() {
                    active(_this.config.current)
                    if (_this.config.current >= carousel_img_length) {
                        carousel_img_ul.css('left', - _this.config.w)
                        _this.config.current = 0
                        active(_this.config.current)
                    }
                })
            }
        }
        // 重置计时器
        function toggleInterval () {
            clearInterval(carouselInt)
            carouselInt = setInterval(carouselInterval, _this.config.intervalTime)
        }

    } // --end-- xcarousel

    $.fn.extend({
        xcarousel: function() {
            new xcarousel($(this))
        }
    })

})(jQuery)

var config = {
    current: 0,
    speed: 500,
    intervalTime: 2000
}

$('.carousel').xcarousel(config)

//移动设备旋转事件
window.addEventListener("orientationchange", function() {
  //  $(".carousel-dot ul").html("");
 //   $('.carousel').xcarousel(config);
  //  alert("没事干别老转屏幕,mmp")
}, false);