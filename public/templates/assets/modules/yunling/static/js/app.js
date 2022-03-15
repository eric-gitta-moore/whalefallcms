/*
*公共方法Ajax=post提交数据
*url:提交地址，data:数据对象
*/
function Ajax_query(url,data){
	if(!data){
		data = $('form').serialize();
	}
	if(data && url){
		pload(true);
		$.post(url,data,function(d){
			console.log(d);
			if(d){
				pload(false);
				if(d.status){
					salert(d.info);
					setTimeout(function(){
						if(d.url){
							location.href=d.url
						}
					},2000);
				}else{
					salert(d.info);
				}
			}else{
				salert('请求失败!')
			}
		});
	}
}


//校验手机号
function checkMobile(mobile){
	var msg='';
	var myreg = /^1[34578]\d{9}$/;             
	if(mobile == ''){
		msg = '请输入您的手机号！';
	}else if(mobile.length !=11){
		msg = '您的手机号输入有误！';
	}else if(!myreg.test(mobile)){
		msg = '请输入有效的手机号！';
	}
	if(msg!=''){
		pload(false);
		salert(msg);
		return false;
	}else{
		return true;
	}
}


//身份证验证
function checkCardNo(code) { 
		var city={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江 ",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北 ",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏 ",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外 "};
		var msg = "";
		if(!code || !/^[1-9][0-9]{5}(19[0-9]{2}|200[0-9]|2010)(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[01])[0-9]{3}[0-9xX]$/i.test(code)){
			msg = "身份证号格式错误";
		}else if(!city[code.substr(0,2)]){
			msg = "地址编码错误";
		}else{
			//18位身份证需要验证最后一位校验位
			if(code.length == 18){
				code = code.split('');
				//∑(ai×Wi)(mod 11)
				//加权因子
				var factor = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ];
				//校验位
				var parity = [ 1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2 ];
				var sum = 0;
				var ai = 0;
				var wi = 0;
				for (var i = 0; i < 17; i++)
				{
					ai = code[i];
					wi = factor[i];
					sum += ai * wi;
				}
				var last = parity[sum % 11];
				if(parity[sum % 11] != code[17]){
					msg = "校验位错误";
				}
			}
		}
		if(msg!=''){
			pload(false);
			salert(msg);
			return false;
		}else{
			return true;
		}
}

/*
*注册发送验证码
*ob:发送按钮元素
*/
var flag = true;
function sendSms(ob){
	if(flag){
		var mobile = $('#mobile').val();
		if(checkMobile(mobile)){
			$.post("./index.php?m=&c=Index&a=SendSms",{mobile:mobile},function(d){
				if(d){
					if(d.status){
						salert(d.info);
						Settime(ob);
					}else{
						pload(false);
						salert(d.info);
					}
				}else{
					pload(true);
					salert('请求失败！');
				}
			});
		}
	}
}



//验证码倒计时
var countdown = 30;
function Settime(ob) {
    if (countdown == 0) {
        $(ob).html("获取验证码");
        countdown = 30;
		flag = true;
		$(ob).css('background','#3679ff');
        return;
    } else {
        $(ob).html(countdown+'S');
		$(ob).css('background','#9E9E9E');
        countdown--;
		flag = false;
    }
    setTimeout(function () {
        Settime(ob);
    }, 1000);
}


//登录
/*function login(){
	var mobile = $('#mobile').val();
		pass = $('#pass').val();
	if(checkMobile(mobile)){
		Ajax_query('index.php?m=Home&c=MhPublic&a=login',{mobile:mobile,pass:pass,fr:fr});
	}
}*/

//弹出消息
function salert(msg){
	var html = '<div class="mint-toast is-placemiddle" id="msg" style="padding: 10px; display: block;">'
			+'<span class="mint-toast-text" style="padding-top: 0px;">'
			+msg
			+'</span>'
			+'</div>';
	$('body').append(html);
	setTimeout(function(){
		if($('#msg').length>0){
			$('#msg').remove();
		}
	},2000);
}

//加载曾
function pload(fg){
	if(fg){
		var html='<div class="mint-indicator" id="loading">'
			+'<div class="mint-indicator-wrapper" style="padding: 15px;">'
			+'<span class="mint-indicator-spin">'
			+'<div class="mint-spinner-snake" style="border-top-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); height: 32px; width: 32px;"></div>'
			+'</span>' 
			+'</div>'
			+'<div class="mint-indicator-mask"></div>'
			+'</div>';
		$('body').append(html);
	}else{
		if($('#loading').length>0){
			$('#loading').remove();
		}
	}
	
}

