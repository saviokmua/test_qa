$(document).on("ready",function() {

	$('body').on("click",".btn-card-generator",function(event){
		event.preventDefault();
		number=Math.floor(Math.random()*10000000000);
		serial=Math.floor(Math.random()*10000000000);
		$("#number").val(number);
		$("#serial").val(serial);
	});


	$('body').on("click",".btn-card-update-back",function(event){
		event.preventDefault();
		window.location.replace("http://"+window.location.hostname+"/cards");
	});

	$('body').on("click",".btn-card-generator-submit",function(event){
		event.preventDefault();
		$("#form-card-create").submit();
	});

	$('body').on("click",".btn-card-update-submit",function(event){
		event.preventDefault();
		$("#form-card-update").submit();
	});


	$('.datepicker').datepicker({format: 'yyyy-mm-dd'});

	$('body').on('click','.btn-search-reset',function(event){
		event.preventDefault();
		$("form#form-card-search :input").each(function(){
			$(this).val('');
		});
		$("#form-card-search").submit();
	});

	$("#form-card-create").validate({
		rules: {
			"generator[number]": {
				required:true,
				remote: {
					url: "/card/check/number",
					type: "post"
				}
			},
			"generator[serial]": {
				required:true,
				remote: {
					url: "/card/check/serial",
					type: "post"
				}
			},
			"generator[date_exp]": {
				required:true,
			}		
		},
		messages: {
			"generator[number]":{
				required: "Enter a Number",
				remote: jQuery.validator.format("{0} is already in use")
			},
			"generator[serial]":{
				required: "Enter a Serial",
				remote: jQuery.validator.format("{0} is already in use")
			},
			"generator[date_exp]":{
				required: "Select a Date Expiration"
			}
		}
	});
	$("#form-purshare-create").validate({
		rules: {
			"purshare[create_at]": {
				required:true
			},
			"purshare[card_id]": {
				required:true
			},
			"purshare[summa]": {
				required:true
			}

		}
	})

});