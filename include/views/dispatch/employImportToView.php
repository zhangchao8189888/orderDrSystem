<div id="content-header">
    <div id="breadcrumb">
        <a href="/index.php" title="返回首页" class="tip-bottom"><i class="icon-home"></i>首页</a>
        <a href="/product/" class="current">派遣管理</a>
        <a href="/product/productList" class="current">花名册导入结果</a>
    </div>
</div>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="controls">
                    <span>身份证号<input style="width:20px;" class="click_position" id='e_num' value="0"/>列</span>
                    <span>姓名<input style="width:20px;" class="click_position" id='e_name'value="0"/>列</span>
                    <span>合同号<input style="width:20px;" class="click_position" id='e_hetong_num'value="0"/>列</span>
                    <span>身份类别<input style="width:20px;" class="click_position" id='e_type'value="0"/>列</span>
                </div>
                <div class="controls">
                    <select id="custom_id">
                        <option value="-1">选择单位</option>
                        <?php
                            foreach($custom_list as $val) {
                                echo '<option value="'.$val['id'].'">'.$val['name'].'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="widget-box">
                <div class="controls">
                    <div style="float: right;margin-right: 5px"><span id="add_btn" class="btn btn-success"/>保存</span></div>
                </div>
            </div>
            <div class="widget-box">
                <div class="controls">
                    <!-- checked="checked"-->
                    <!--<input type="button" value="保存" class="btn btn-success" id="produceSave" >-->
                    <input type="checkbox" id="colHeaders" autocomplete="off"> <span>锁定前<input style="width:20px;"  id='clo_w'value="2"/>列</span>
                    <input type="checkbox" id="rowHeaders" autocomplete="off"> <span>锁定前<input style="width:20px;" id='clo_h'value="2"/>行</span>
                    &nbsp;&nbsp;选中行数：<span style="color: #049cdb" id="p_num"></span>&nbsp;&nbsp;选中合计：<span id="p_sum" style="color: #049cdb"></span>
                </div>
                <div id="excelGrid" class="dataTable" style="width:1000px;height: 500px; overflow: hidden"></div>
            </div>
        </div>

    </div>
</div>
<script>
    //var ListJson = <?php //echo preg_replace("#^{(*.)}$#","[$1]",json_encode($list));?>;
    var ListJson = <?php echo $list;?>;
    var withJson = <?php echo $head_width;?>;
    var heightJson = <?php echo $head_height;?>;
</script>
<link href='<?php echo FF_STATIC_BASE_URL;?>/css/custom.css' rel='stylesheet' type='text/css' />
<script src="<?php echo FF_STATIC_BASE_URL;?>/js//hot-js/handsontable.full.js"></script>
<link rel="stylesheet" media="screen" href="<?php echo FF_STATIC_BASE_URL;?>/js/hot-js/handsontable.full.css">
<script type="text/javascript" src="<?php echo FF_STATIC_BASE_URL;?>/common-js/zq.excel_view.js"></script>
