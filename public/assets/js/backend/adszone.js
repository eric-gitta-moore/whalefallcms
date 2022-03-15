define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

	var Controller = {
		index: function () {
			// 初始化表格参数配置
			Table.api.init({
				extend: {
					index_url: 'adszone/index',
					add_url: 'adszone/add',
					edit_url: 'adszone/edit',
					del_url: 'adszone/del',
					multi_url: 'adszone/multi',
					table: 'adszone_zone',
				}
			});
			var table = $("#table");

			// 初始化表格
			table.bootstrapTable({
				url: $.fn.bootstrapTable.defaults.extend.index_url,
				pk: 'id',
				sortName: 'weigh',
				columns: [
					[
						{checkbox: true},
						{field: 'id', title: __('Id')},
						{field: 'name', title: "广告位名称"},
						{field: 'mark', title: "广告位标记"},
						{field: 'type', title: "广告位类型", formatter: function (value, row, index) {
								let adsType = ["", "图片广告", "多图&幻灯广告", "代码广告"];
								return '' + adsType[value];
							}},
						{field: '', title: "广告位尺寸", formatter: function (value, row, index) {
								if (row.type == 3) {
									return "--";
								} else {
									return 'w:' + row.width + ' X h:' + row.height;
								}
							}},
						{field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
						{
							field: 'operate',
							title: __('Operate'),
							buttons: [
								{
									name: 'adszone',
									title: "广告管理",
									text: "广告管理",
									extend: "data-area='[\"\90%\"\,\"\90%\"\]'",
									url: 'adszone/ads',
									icon: 'fa fa-table',
									classname: 'btn btn-info btn-xs btn-execute btn-dialog'
								},
							],
							table: table,
							events: Table.api.events.operate,
							//formatter: Table.api.formatter.operate
							formatter: function (value, row, index) {
								var that = $.extend({}, this);
								var table = $(that.table).clone(true);
								if (row.type == 3) {
									$(table).data("operate-adszone", null);
								}
								that.table = table;
								return Table.api.formatter.operate.call(that, value, row, index);
							}
						}
						//{field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
					]
				]
			});

			// 绑定TAB事件
			$('.panel-heading a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				var field = $(this).closest("ul").data("field");
				var value = $(this).data("value");
				var options = table.bootstrapTable('getOptions');
				options.pageNumber = 1;
				options.queryParams = function (params) {
					var filter = {};
					if (value !== '') {
						filter[field] = value;
					}
					params.filter = JSON.stringify(filter);
					return params;
				};
				table.bootstrapTable('refresh', {});
				return false;
			});

			// 为表格绑定事件
			Table.api.bindevent(table);
		},
		add: function () {
			Controller.api.bindevent();
			$(".form-group .form-input-type").on("click", function (e) {
				var _value = $(this).val();
				if (_value == "3") {
					$(".form-group-width").hide();
					$(".form-group-height").hide();
					$(".form-group-code").show();
				} else {
					$(".form-group-width").show();
					$(".form-group-height").show();
					$(".form-group-code").hide();
				}
			});

		},
		edit: function () {
			Controller.api.bindevent();
			$(".form-group .form-input-type").on("click", function (e) {
				var _value = $(this).val();
				if (_value == "3") {
					$(".form-group-width").hide();
					$(".form-group-height").hide();
					$(".form-group-code").show();
				} else {
					$(".form-group-width").show();
					$(".form-group-height").show();
					$(".form-group-code").hide();
				}
			});
		},
		api: {
			bindevent: function () {
				Form.api.bindevent($("form[role=form]"));
			}},
		ads: function (data) {
			// 初始化表格参数配置
			//console.log(ids)
			var ids = $("#assign-data-ids").val();

			Table.api.init({
				extend: {
					index_url: 'adszone/ads/ids/' + ids,
					add_url: 'adszone/ads_add/aid/' + ids,
					edit_url: 'adszone/ads_edit/ids/' + ids,
					del_url: 'adszone/ads_del',
					table: 'adszone_ads',
				}
			});

			var table = $("#table");
			// 初始化表格
			table.bootstrapTable({
				url: $.fn.bootstrapTable.defaults.extend.index_url,
				pk: 'id',
				sortName: 'weigh',
				columns: [
					[
						{checkbox: true},
						{field: 'id', title: __('Id')},
						{field: 'title', title: "广告标题 "},
						{field: 'imageurl', title: "广告图片", formatter: Table.api.formatter.image},
						{field: 'zone_id', title: '广告位ID'},
						{field: 'effectime', title: "生效时间", formatter: Table.api.formatter.datetime},
						{field: 'expiretime', title: "到期时间", formatter: Table.api.formatter.datetime},
						//{field: 'weigh', title: "广告权重"},
						{field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
						{
							field: 'operate',
							title: __('Operate'),
							table: table,
							events: Table.api.events.operate,
							formatter: Table.api.formatter.operate
						}
						//{field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
					]
				]
			});

			// 绑定TAB事件
			$('.panel-heading a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				var field = $(this).closest("ul").data("field");
				var value = $(this).data("value");
				var options = table.bootstrapTable('getOptions');
				options.pageNumber = 1;
				options.queryParams = function (params) {
					var filter = {};
					if (value !== '') {
						filter[field] = value;
					}
					params.filter = JSON.stringify(filter);
					return params;
				};
				table.bootstrapTable('refresh', {});
				return false;
			});
			// 为表格绑定事件
			Table.api.bindevent(table);
		},
		ads_add: function () {
			Controller.api.bindevent();
			$(".field-category").on("click", function (e) {
				var _value = $(this).val();
				var required = $(this).attr("data-required");
				var required_list = required.split(",");
				$("#field-suffix").val("");
				$("#field-type").val("");
				$(".form-input").hide();
				$(".form-input input").attr("disabled", "disabled");
				$(".form-input input").attr("readonly", "readonly");
				for (let value of required_list) {
					$(".form-input-" + value).show();
					$(".form-input-" + value + " input").attr("disabled", false);
					$(".form-input-" + value + " input").attr("readonly", false);
				}
			});
			$(".form-selection").on("change", function (e) {
				var _value = $(this).val();
				if (_value == "list-enum" || _value == "list-set" || _value == "data-enum" || _value == "data-set" || _value == "enum" || _value == "set") {
					$(".form-input-comment").show();
					$(".form-input-comment input").attr("disabled", false);
					$(".form-input-comment input").attr("readonly", false);
				} else {
					$(".form-input-comment").hide();
					$(".form-input-comment input").attr("disabled", "disabled");
					$(".form-input-comment input").attr("readonly", "readonly");
				}
			});

			$("#field-suffix").on("change", function (e) {
				var _value = $(this).val();
				if (_value == "content") {
					$("#row-length").attr("disabled", "disabled");
					$("#row-default").attr("disabled", "disabled");
				} else {
					$("#row-length").attr("disabled", false);
					$("#row-default").attr("disabled", false);
				}
			});
			$("#field-type").on("change", function (e) {
				var _value = $(this).val();
				if (_value == "text") {
					$("#row-length").attr("disabled", "disabled");
					$("#row-default").attr("disabled", "disabled");
				} else {
					$("#row-length").attr("disabled", false);
					$("#row-default").attr("disabled", false);
				}
			});
			$(".field-category:checked").click();
		},
		ads_edit: function () {
			Controller.api.bindevent();
		},
	};
	return Controller;
});
