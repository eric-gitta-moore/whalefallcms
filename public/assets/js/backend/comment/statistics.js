define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'echarts', 'echarts-theme'], function ($, undefined, Backend, Table, Form, Echarts, undefined) {

    var Controller = {
        index: function () {
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {},
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: Config.statistics.columns
                },
                yAxis: {},
                grid: [{
                    left: 'left',
                    top: 'top',
                    right: '10',
                    bottom: 30
                }],
                series: [
                    {
                        name: __('Comments'),
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {}
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: Config.statistics.data
                    }]
            };

            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);

            $(window).resize(function () {
                myChart.resize();
            });

            Form.api.bindevent($("form[role=form]"));

            $(document).on("click", ".btn-filter", function () {
                var label = $(this).text();
                var obj = $(".datetimerange").data("daterangepicker");

                var dates = obj.ranges[label];
                obj.startDate = dates[0];
                obj.endDate = dates[1];

                obj.clickApply();
            });
            $(".datetimerange").on("blur", function () {
                Fast.api.ajax({
                    url: 'comment/statistics/index',
                    data: {date: $(this).val()}
                }, function (data) {
                    myChart.setOption({
                        xAxis: {
                            data: data.columns
                        },
                        series: [{
                            name: __('Comments'),
                            data: data.data
                        }]
                    });
                    return false;
                });
            });
            $(document).on("click", ".btn-refresh", function () {
                $(".datetimerange").trigger("blur");
            });
            //每隔一分钟定时刷新图表
            setInterval(function () {
                $(".btn-refresh").trigger("click");
            }, 60000);
        }
    };
    return Controller;
});