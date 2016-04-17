/**
 * Created by zhangchao8189888 on 16-4-1.
 */
$(function () {
    var
        container = document.getElementById('excelGrid'),position_val,
        hot5;
    Handsontable.hooks.add('afterGetColHeader', function (col, TH) {
        if (col >= 0) {//this.getSettings().columnSorting &&
            Handsontable.Dom.addClass(TH.querySelector('.colHeader'), 'columnClick');
        }
    });
    function createBigData() {
        hot5.loadData(ListJson);


        bindColumnSortingAfterClick.call(hot5);
    }
    $(".click_position").on('focus',function () {
        position_val = $(this).attr("id");
    });
    var selectFirst = document.getElementById('selectFirst'),
        rowHeaders = document.getElementById('rowHeaders'),
        colHeaders = document.getElementById('colHeaders');

    Handsontable.Dom.addEvent(colHeaders, 'click', function () {
        if (this.checked) {
            var i = parseInt($("#clo_w").val());
            hot5.updateSettings({
                fixedColumnsLeft: i
            });
        } else {
            hot5.updateSettings({
                fixedColumnsLeft: 0
            });
        }

    });
    Handsontable.Dom.addEvent(rowHeaders, 'click', function () {
        if (this.checked) {
            var i = parseInt($("#clo_h").val());
            hot5.updateSettings({
                fixedRowsTop: i
            });
        } else {
            hot5.updateSettings({
                fixedRowsTop: 0
            });
        }

    });
    var bindColumnSortingAfterClick = function () {
        var instance = this;

        var eventManager = Handsontable.eventManager(instance);
        eventManager.addEventListener(instance.rootElement, 'click', function (e){
            if(Handsontable.Dom.hasClass(e.target, 'columnClick')) {
                var col = getColumn(e.target)+1;
                $("#"+position_val).val(col);

            }
        });

        function countRowHeaders() {
            var THs = instance.view.TBODY.querySelector('tr').querySelectorAll('th');
            return THs.length;
        }

        function getColumn(target) {
            var TH = Handsontable.Dom.closest(target, 'TH');
            return Handsontable.Dom.index(TH) - countRowHeaders();
        }
    };
    hot5 = Handsontable(container, {
        data: [],
        startRows: 5,
        startCols: 4,
        rowHeaders: true,
        colHeaders: true,
        minSpareRows: 1,
        comments: true,
        manualColumnResize: true,
        contextMenu: true
    });
    createBigData();
    var redRenderer = function (instance, td, row, col, prop, value, cellProperties) {
        Handsontable.renderers.TextRenderer.apply(this, arguments);
        td.style.color = 'red';

    };
    $("#add_btn").click(function () {
        if ($("#e_num").val() == 0 || $("#e_hetong_num").val() == 0 || $("#e_name").val() == 0 || $("#e_type").val() == 0 || $("#custom_id").val() == -1) {
            alert("保存前请完善信息");
            return;
        }
        var data = hot5.getData();
        var formData = {
            data: data,
            e_num: $("#e_num").val(),
            e_name: $("#e_name").val(),
            e_type: $("#e_type").val(),
            e_hetong_num: $("#e_hetong_num").val(),
            custom_id: $("#custom_id").val()
        }
        $.ajax(
            {
                type: "post",
                url:GLOBAL_CF.DOMAIN+"/dispatch/saveEmployList",
                data: formData,
                dataType: "json",
                success: function(res){
                    if (res.status > 100000) {
                        if (res.status == 100002) {
                            alert(res.content);
                            return;
                        } else if (res.status == 100001) {

                            var error_list = res.content.error_list;
                            var success_list = res.content.success_list;
                            for (var i = 0; i < success_list.length; i++) {
                                var row = success_list[i];
                                hot5.alter ('remove_row',row);

                            }
                            if ($.util.isArray(error_list) && error_list.length > 0 ) {
                                var cell_pro = [],bit = 0;
                                for (var i = 0; i < error_list.length; i++) {
                                    var error_obj = error_list[i];
                                    if (error_obj.message.message) {
                                        bit = $("#e_num").val()-1;
                                        cell_pro.push(
                                            {row: error_obj.key, col:bit, renderer: redRenderer,comment: error_obj.message.message}
                                        );
                                    }

                                }
                                hot5.updateSettings({
                                    cell: cell_pro
                                })
                            }
                        }
                    } else {
                        alert(res.content);
                    }

                    return;


                }
            }
        );
    });
});