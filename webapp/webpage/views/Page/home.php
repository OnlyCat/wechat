
<div class="h-carousel">
    <div class="h-c-box">
        <ul>
            <li><img src=""></li>
            <li><img src=""></li>
            <li><img src=""></li>
        </ul>
    </div>
</div>
<div class="h-b-menu">
    <div class="h-m-box">
        <ul>
            <li class="btn-jump spend">
                <dl>
                    <dt><img src="/static/webapp/img/icon/flat/icon_34.gif"></dt>
                    <dd>账 单</dd>
                </dl>
            </li>
            <li>
                <dl class="btn-jump questions">
                    <dt><img src="/static/webapp/img/icon/flat/icon_38.gif"></dt>
                    <dd>题 库</dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt><img src="/static/webapp/img/icon/flat/icon_87.gif"></dt>
                    <dd>占位1-3</dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt><img src="/static/webapp/img/icon/flat/icon_66.gif"></dt>
                    <dd>占位1-4</dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt><img src="/static/webapp/img/icon/flat/icon_67.gif"></dt>
                    <dd>占位2-1</dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt><img src="/static/webapp/img/icon/flat/icon_86.gif"></dt>
                    <dd>占位2-2</dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt><img src="/static/webapp/img/icon/flat/icon_74.gif"></dt>
                    <dd>占位2-3</dd>
                </dl>
            </li>
            <li>
               <dl>
                    <dt><img src="/static/webapp/img/icon/flat/icon_72.gif"></dt>
                    <dd>占位2-4</dd>
              </dl>
            </li>
        </ul>
    </div>
</div>
<!-- 账单模块 -->
<?php
$spendTmplate = <<< EOF
    <div class="bill-box">
        <div class="new-bill-confirm">
            <div class="n-b-c-title">
                <div class="n-c-t-icon">
                    <i class="fa fa-file-text-o"></i>
                </div>
                <div class="n-c-t-text">
                    <div class="n-c-t-title">
                        账单助手
                    </div>
                    <div class="n-c-t-time">
                      %s
                    </div>
                </div>
                <div class="n-c-t-prompt">
                    <i class="fa fa-ellipsis-h"></i>
                </div>
            </div>
            <div class="n-b-c-content">
                <div class="n-b-c-line">
                    <div class="n-b-c-l">
                        账单金额
                    </div>
                    <div class="n-b-c-r">
                        <span class="n-b-c-l-m">￥%s</span>
                    </div>
                </div>
                <div class="n-b-c-line">
                    <div class="n-b-c-l">
                        账单日期
                    </div>
                    <div class="n-b-c-r">
                     %s
                    </div>
                </div>
                <div class="n-b-c-line">
                    <div class="n-b-c-l">
                        账单种类
                    </div>
                    <div class="n-b-c-r">
                        %s·%s
                    </div>
                </div>
                <div class="n-b-c-line">
                    <div class="n-b-c-l">
                        账单备注
                    </div>
                    <div class="n-b-c-r">
                       %s
                    </div>
                </div>
            </div>
        </div>
    </div>
EOF;
    if($spend){
        printf($spendTmplate, $spend->inserttime, $spend->money,  $spend->spendtime, $spend->classify, $spend->childr, $spend->descripe);
    }
?>
<!-- 新闻模块-->
<?php
if(isset($news)){
    echo '<div class="news-box">';
        echo '<div class="news-title"><span></span><?php' . $area .'资讯</div>';
            $newsPicTmplate = <<< EOF
                    <div class='news-item'>
                      <a href='%s'>
                            <div class='t-a-title'>%s</div>
                            <div class='text-area'>
                                <div class='img-area'>
                                    <img src =%s>
                                </div>
                                <div class='t-a-content'>%s</div>
                            </div>
                        </a>
                    </div>
EOF;
        $newsTmplate = <<< EOF
           
               <div class='news-item'>
                    <a href='%s'>
                        <div class='t-a-title'>%s</div>
                        <div class='text-t-area'> 
                            <div class='t-a-content'>%s</div>
                        </div>
                   </a>
              </div>
           
EOF;
            foreach ($news as $item){
                if(empty( $item['img'])) {
                    printf($newsTmplate, $item['url'], strip_tags($item['title']), mb_substr(strip_tags($item['content']), 0, 100) ."...");
                } else {
                    printf($newsPicTmplate, $item['url'], strip_tags($item['title']), $item['img'], mb_substr(strip_tags($item['content']), 0, 70) ."...");
                }

            }
            echo "</div>";
        }

?>


<script>
    $(document).ready(function(){
        //轮播图效果
        imgCount = $(".h-carousel .h-c-box ul li").length;
        $(".h-carousel .h-c-box ul li").width(window.innerWidth)
        $(".h-carousel .h-c-box").width($(".h-carousel .h-c-box ul li").width() * imgCount);
        seat = 0;
        swing = 1;
        setInterval(
            function() {
                if (seat >= $(".h-carousel .h-c-box").width() -  $(".h-carousel .h-c-box ul li").width()) {
                    swing = 0;
                } else if(seat <= 0){
                    swing = 1;
                }
                if(swing == 1){
                    seat += $(".h-carousel .h-c-box ul li").width();
                } else if(swing == 0){
                    seat -= $(".h-carousel .h-c-box ul li").width();
                }
                $(".h-carousel .h-c-box").animate({left:seat * -1 },500);
            },5000);
})
</script>
