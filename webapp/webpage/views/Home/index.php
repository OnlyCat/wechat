<!DOCTYPE html> <html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="format-detection" content="telephone=no">
        <link rel="shortcut icon" href="http://p.www.xiaomi.com/favicon.ico" type="/image/x-icon">
        <link rel="apple-touch-icon-precomposed" href="http://a.tbcdn.cn/mw/s/hi/tbtouch/images/touch-icon.png">
        <meta http-equiv=”X-UA-Compatible” content=”IE=edge,chrome=1″/>
        <title>猫先生实验室</title>
        <link  rel="stylesheet" href="/static/webapp/css/font-awesome.min.css">
        <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
        <script src="/static/webapp/js/jquery-3.2.1.min.js"></script>
        <script src="/static/webapp/js/jquery.touchSwipe.min.js"></script>
        <script src="/static/webapp/js/echarts.min.js"></script>

        <script src="/static/webapp/js/sweetalert.min.js"></script>
        <link  rel="stylesheet" href="/static/webapp/css/sweetalert.css">
        <link  rel="stylesheet" href="/static/webapp/css/site.css">
        <link  rel="stylesheet" href="/static/webapp/css/style-sample-1.css">
    </head>
    <body>
        <?php echo $loading??"加载中..."; ?>
        <div class="wrap">
            <div class="header">
                <?php echo $header??"加载中..."; ?>
            </div>
            <div class="content">
                <?php echo $default??"加载中..."; ?>
            </div>
            <div class="footer">
                <?php echo $footer??"加载中..."; ?>
            </div>
            <div class="l-side-bar">

            </div>
            <div class="bg-shade"></div>
        </div>
    </body>
</html>

<script>
  wx.config({
      debug: false,
      appId: "<?php echo $appid; ?>",
      timestamp: "<?php echo $timestamp; ?>",
      nonceStr: "<?php echo $noncestr; ?>",
      signature: "<?php echo $signature; ?>",
      jsApiList: [
        'checkJsApi',
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareWeibo',
        'onMenuShareQZone',
        'hideMenuItems',
        'showMenuItems',
        'hideAllNonBaseMenuItem',
        'showAllNonBaseMenuItem',
        'translateVoice',
        'startRecord',
        'stopRecord',
        'onVoiceRecordEnd',
        'playVoice',
        'onVoicePlayEnd',
        'pauseVoice',
        'stopVoice',
        'uploadVoice',
        'downloadVoice',
        'chooseImage',
        'previewImage',
        'uploadImage',
        'downloadImage',
        'getNetworkType',
        'openLocation',
        'getLocation',
        'hideOptionMenu',
        'showOptionMenu',
        'closeWindow',
        'scanQRCode',
        'chooseWXPay',
        'openProductSpecificView',
        'addCard',
        'chooseCard',
        'openCard'
      ]
  });

</script>
<script src="/static/webapp/js/site.js"></script>
<script src="/static/webapp/js/script-sample-1.min.js"></script>