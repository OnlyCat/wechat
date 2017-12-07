<div class="m-title">
    <ul>
        <li id="a-s-c">智能分析</li>
        <li id="c-s-d" class="active" >今日账单</li>
        <li id="h-s-p">历史账单</li>
    </ul></div>
<div class="spend-main block-0">
    <div class="s-m-title-0">
    </div>
</div>
<div class="spend-main block-1">
    <div class="s-m-title">
        <div class="m-content">
            <div class="m-c-text">
                <div class="m-c-t-title">
                    花费 (元)
                </div>
                <div class="m-c-t-content">
                    0.00
                </div>
            </div>
        </div>
    </div>
    <div class="s-m-content">
        <div class="s-m-c-title">
            账单明细
        </div>
        <div class="s-m-c-content">
            <ul>
            </ul>
        </div>
    </div>
    <div class="s-m-ensure">
        <div class="m-e-title"> 确认账单 </div>
        <div class="m-e-classify">
            <ul>
            </ul>
        </div>
        <div class="m-e-childer">
            <ul>
            </ul>
        </div>
        <div class="m-e-remarks">
            <div class="r-m-icon calendar"><i class="fa fa-calendar"></i></div>
            <div class="r-m-text"><input type="time" name ="calendar"></div>
        </div>
        <div class="m-e-remarks">
            <div class="r-m-icon remark"><i class="fa fa-edit"></i></div>
            <div class="r-m-text"><input type="text" name ="remark" placeholder ="备注信息"></div>
        </div>
        <div class="m-e-remarks">
            <div class="r-m-icon division"><i class="fa fa-tasks"></i></div>
            <div class="r-m-text">
                <input name="spendid" type="hidden">
                <input type="text" name ="division" placeholder ="账单种类" readonly >
            </div>
        </div>
        <div class="m-e-money">
            <div class="m-money-area"><span class="symbol">￥</span><input name="money" type="text" value="0.0"></div>
            <div class="m-money-area "><button>确 定</button></div>
        </div>
    </div>
</div>
<div class="spend-main block-2">
    <div class="s-m-title-spend">
        <span class="btn-condition">
            <div class="select-area">
                <ul>
                    <li>
                        <div class="s-a-title">开始日期</div>
                        <div class="s-a-input"><input type="date" name="start_date"></div>
                    </li>
                    <li>
                        <div class="s-a-title">结束日期</div>
                        <div class="s-a-input"><input type="date"  name="stop_data"></div>
                    </li>
                    <li>
                        <div class="s-a-title">类别</div>
                        <div class="s-a-input">
                            <div class="btn-input">
                                <span class="btn-module b-l active">年</span>
                                <span class="btn-module b-m">月</span>
                                <span class="btn-module b-r">日</span>
                            </div>
                        </div>
                     </li>
                </ul>
            </div>
            <span class="cond-title">2017-2018年账单</span>
            <span class="fa fa-chevron-down"></span>
        </span>
    </div>
</div>

<script>

    $(document).ready(function () {
            //获取当日账单数据
            var url = window.location.origin;
            var param = window.location.search;
            $(".s-m-c-content ul").html("");
            uri = "/spend/daySpend"
            daySpendUrl = url + uri + param;
            $.get(daySpendUrl, function (result) {
                result = JSON.parse(result);
                var money = 0;
                for (item in result) {
                    money += parseFloat(result[item]['money']);
                    var c_1 = typeof(result[item]['classify'])=="undefined" ? "其他" : result[item]['classify']
                    var c_2 = typeof(result[item]['childr'])=="undefined" ? "其他" : result[item]['childr']
                    node = "<li><div id=" + result[item]['id'] + " class=\"s-c-title\" attr-info = "+ c_1 + '·' + c_2 +" style=\"background:#" + (c_1.charCodeAt(1) * 100 + c_2.charCodeAt(1)).toString(16) + "\"  >"+c_2.slice(0,1) +"</div>";
                    if(result[item]['isconfirm'] == 1){
                        node += "<div class=\"s-c-explain\">" ;
                    } else {
                        node += "<div class=\"s-c-explain btn-confirm\">";
                    }
                    node = node +"<div class=\"s-c-e-class\">" + result[item]['descripe']
                        + "</div><div class=\"s-c-e-time\">" +
                        result[item]['spendtime'].substr(11)
                        + "</div></div><div class=\"s-c-money\">"
                        + parseFloat(result[item]['money']).toFixed(2)
                        + "</div></li>";
                    $(".s-m-c-content ul").append(node);
                }
                $(".m-c-t-content").html(parseFloat(money).toFixed(2));
            });
    })
</script>