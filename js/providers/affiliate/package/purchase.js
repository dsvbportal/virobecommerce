(function(){
	var purchaseObj = {};
	var packsObj = [];
	var purchaseObj = {};
	var walObj = {};
	var curPack = {};
	var curPanel = '';
	
	$.fn.searchPack = function(){
		var op = {};
		var id =  $(this).data('id');
		$.each(packsObj,function(k,elm){
			if(elm.package_code ==id){	
			console.log(elm)
				op.package_code =  elm.package_code;
				op.package_id =  elm.package_id;
				op.package_name =  elm.package_name;
				op.currency_id =  elm.currency_id;
				op.price =  elm.price;
				op.fprice =  elm.fprice;
				op.currency_code =  elm.currency_code;
			}			
		});		
		return op;
	};	
	
	$('.buy_now').each(function(){	  
		packsObj.push($(this).data('info'));	
	});
	
	$('.buy_now').removeAttr('data-info');	

	$('#paymodes').on('click','.backto_packagebtn',function(e){
		e.preventDefault();															 	
		$('#paymodes').pageSwapTo('#packagegrid');
		$('#paymentprocess').show();
		$('.paymode').remove();		
	});
	
	$('.pricing-column').on('click','.package_more_details',function(e){
		e.preventDefault();															 	
		$('.package_more_section',$(this).closest('.pricing-column')).show();
	});
	
	$('.pricing-column').on('click','.package_more_section .closeBtn',function(e){
		e.preventDefault();
		$(this).parent().hide();
	});
		
	
	$('#package_purchase').on('click','.buy_now',function(evt){
		evt.preventDefault();	
		var curBtn = $(this);
		loadPreloader();		
		$.ajax({
			url:$(this).attr('href'),
			method:'POST',
			data:{id:$(this).attr('data-id')},
			dataType:'json',		
			success:function(op){			
				purchaseObj = curBtn.searchPack();
				$('#package_purchase .selpaymode').empty();
				$.each(op.purchase_paymodes,function(k,elm){
					$('#package_purchase .selpaymode').append(
						$('<li>',{class:'col-lg-3'}).append(
							$('<a>',{name:'payment_gateways',class:'paymode_types',href:elm.url,'data-id':elm.payment_type_id}).append(
								$('<img>',{src:elm.icon}),
								$('<h4>').text(elm.payment_type),
								$('<p>').text(elm.description),
							)
						)
					)
				});					
				$('#packInfo .pkname').text(purchaseObj.package_name);
				$('#packInfo .pkamt').text(purchaseObj.fprice+' '+purchaseObj.currency_code);								
				$('#packagegrid').pageSwapTo('#paymodes');
			}
		});
	});
	
	$('#package_purchase').on('click',".paymode_types",function (evt) {
		evt.preventDefault();		
		$('.paymode').hide();
		var curBtn = $(this);
		$.ajax({
			url:$(this).attr('href'),
			method:'POST',
			dataType:'json',			
			success:function(op){			
				purchaseObj.paymode = curBtn.data('id');				
				if(typeof(op.template) != 'undefined' ){
					$('#paymentprocess').after(op.template);
					$('#paymentprocess').pageSwapTo('#walletinfo');					
				} else if(op.gateway_info != undefined ){
					$.paymentGateWay(op.gateway_info);
				}
				else if(typeof(op.redirect) != undefined ){					
					$('#paymentprocess').hide();					
				}				
			}
		});
	});
	
	
	$('#paymodes').on('click','.backto_paymodebtn',function(e){
		e.preventDefault();															 	
		$('#walletinfo').pageSwapTo('#paymentprocess');
	});
	
	var wallet_vallang = {
		'nobal': 'Insufficiant balance'
	};
	
	/*$(document).on('change',"select#wallet_id",function () {
		var id = $(this).val();
		var wallet = {};
		avi_bal = 0;	
        purchaseObj.wallet_id = parseInt($(this).val());	
		$('#walletinfo .balinfo span').hide();
		$('#walletinfo .dedbalinfo').hide();
		$("#walletinfo .balinfo .help-block").remove();		
		$.each(walObj,function(k,elm){
			if(elm.wallet_id==id){
				wallet = elm;
			}
		});	
		console.log(wallet.current_balance + '=' + purchaseObj.price);
		console.log(wallet.current_balance - purchaseObj.price);
		if (wallet && wallet.current_balance >= purchaseObj.price) {		
			$('#walletinfo .balinfo .usrbal').text(wallet.fcurrent_balance);
			$('#walletinfo .balinfo .usrcur').text(wallet.currency_code);
			var bal = wallet.current_balance - purchaseObj.price;
			//bal = bal.toFixed(2);
			//$('#walletinfo .dedbalinfo .usrbal').text(bal);			
			//$('#walletinfo .dedbalinfo .usrbal').text(wallet.current_balance - purchaseObj.price);			
			//$('#walletinfo .dedbalinfo .usrcur').text(wallet.currency_code);
			$('#walletinfo .balinfo span').show();
			$('#walletinfo .dedbalinfo').show();
			$('#walletinfo .panel .panel-body').append($('<div>').addClass('form-group'));
			$('#walletinfo #purchasebyWbtn').show();
			
		} else {
			$('#walletinfo .balinfo span').hide();
			$('#walletinfo .dedbalinfo').hide();
			$('#walletinfo .dedbalinfo .usrbal').text('');
			$('#walletinfo .dedbalinfo .usrcur').text('');
			$('#walletinfo .balinfo').append('<span class="err help-block">'+wallet_vallang.nobal+'</span>');			
		}
	});*/
	
	$('#package_purchase').on('click','#purchasebyWbtn',function(){
		var fdata = $.param(purchaseObj);
		curPanel = $(this).closest('.panel');
		if(fdata != null){
			var curBtn = $(this);
			$.ajax({
				url: curBtn.data('url'),
				method:'POST',
				data: fdata,
				dataType:'json',		
				beforeSend:function(){
					
				},
				success:function(res){	
					if(res.status==200){
						if(res.redirect!=''){
							window.location.href  = res.redirect;
						}
						else {
							$('.panel-body',curPanel).html("<div class='alert alert-"+res.msgtype+"'>"+res.msg+"</div>");					
						}
					} 
				},
				error:function(res){
					
				}
			});
		}
	})	
})(jQuery);