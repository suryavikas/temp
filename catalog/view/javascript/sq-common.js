$(document).ready(function() {
	/* Search */
        $('.button-search').bind('click', function() {
		url = $('base').attr('href') + 'index.php?route=product/search';
				 
		var filter_name = $('input[name=\'filter_name\']').attr('value');
		
		if (filter_name) {
			url += '&filter_name=' + encodeURIComponent(filter_name);
		}
		
		location = url;
	});
	
        $('.pagination .links a').live('click', function() {
            
            var arrData = getFilterParams();
            var getParams = getSortingParams();

            var url = $(this).attr('href');
            
            categoryId = getURLVarFromString(url, "path");
            page = getURLVarFromString(url, "page");
            sendRequest(arrData, getParams, categoryId, "page="+page);
            
            
            return false;
        });


	$('#header input[name=\'filter_name\']').bind('keydown', function(e) {
		if (e.keyCode == 13) {
			url = $('base').attr('href') + 'index.php?route=product/search';
			 
			var filter_name = $('input[name=\'filter_name\']').attr('value');
			
			if (filter_name) {
				url += '&filter_name=' + encodeURIComponent(filter_name);
			}
			
			location = url;
		}
	});
	$('#cart-loading-indicator').ajaxStart(function() {
            $(this).css('display', 'block');
        })
        $('#cart-loading-indicator').ajaxStop(function() {
            $(this).css('display', 'none');
        });

         $('body').live('click', function(event){                       
            $("#cart .heading .content").fadeIn('slow', function(){
            $(this).remove('#cart .heading .content');
            $(this).removeClass('active');
            });
        });

       	/* Ajax Cart */
	$('#cart .heading').live('click', function(event) {
//            event.stopPropagation();
            if($('#cart .heading .content').length == 0){
                if($.trim($('#cart #cart-total').text()) == "Your shopping cart is empty!"){
                    return false;
                }else{
                    $('#cart').addClass('active');

                    $('#cart').load('index.php?route=module/cart #cart');

                    $('#cart').live('mouseleave', function() {
                        $(this).removeClass('active');
                    });
                }
            }
	});
	
	/* Mega Menu */
	$('#menu ul > li > a + div').each(function(index, element) {
		// IE6 & IE7 Fixes
		if ($.browser.msie && ($.browser.version == 7 || $.browser.version == 6)) {
			var category = $(element).find('a');
			var columns = $(element).find('ul').length;
			
			$(element).css('width', (columns * 143) + 'px');
			$(element).find('ul').css('float', 'left');
		}		
		
		var menu = $('#menu').offset();
		var dropdown = $(this).parent().offset();
		
		i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());
		
		if (i > 0) {
			$(this).css('margin-left', '-' + (i + 5) + 'px');
		}
	});

	// IE6 & IE7 Fixes
	if ($.browser.msie) {
		if ($.browser.version <= 6) {
			$('#column-left + #column-right + #content, #column-left + #content').css('margin-left', '195px');
			
			$('#column-right + #content').css('margin-right', '195px');
		
			$('.box-category ul li a.active + ul').css('display', 'block');	
		}
		
		if ($.browser.version <= 7) {
			$('#menu > ul > li').bind('mouseover', function() {
				$(this).addClass('active');
			});
				
			$('#menu > ul > li').bind('mouseout', function() {
				$(this).removeClass('active');
			});	
		}
	}
	
	$('.success img, .warning img, .attention img, .information img').live('click', function() {
		$(this).parent().fadeOut('slow', function() {
			$(this).remove();
		});
	});

     
});

function getURLVar(urlVarName) {
	var urlHalves = String(document.location).toLowerCase().split('?');
	var urlVarValue = '';
	
	if (urlHalves[1]) {
		var urlVars = urlHalves[1].split('&');

		for (var i = 0; i <= (urlVars.length); i++) {
			if (urlVars[i]) {
				var urlVarPair = urlVars[i].split('=');
				
				if (urlVarPair[0] && urlVarPair[0] == urlVarName.toLowerCase()) {
					urlVarValue = urlVarPair[1];
				}
			}
		}
	}
	
	return urlVarValue;
}

function getURLVarFromString(url, urlVarName) {
	var urlHalves = url.toLowerCase().split('?');
	var urlVarValue = '';
	
	if (urlHalves[1]) {
		var urlVars = urlHalves[1].split('&');

		for (var i = 0; i <= (urlVars.length); i++) {
			if (urlVars[i]) {
				var urlVarPair = urlVars[i].split('=');                                
				if (urlVarPair[0] && urlVarPair[0] == urlVarName.toLowerCase()) {                                    
					urlVarValue = urlVarPair[1];
				}
			}
		}
	}

	return urlVarValue;
}

$('.cart .button, .related-product .button').live('click', function() {
    $(this).after('<img id="load-indicator-image" alt="Adding To Cart..." src="catalog/view/theme/squareofone/image/loading.gif" style="margin-left: 10px;"/>');
});

function addToCart(product_id, quantity) {
   

	quantity = typeof(quantity) != 'undefined' ? quantity : 1;

	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: 'product_id=' + product_id + '&quantity=' + quantity,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information, .error').remove();
                        $('#load-indicator-image').remove();
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['success']) {
                                window.location='index.php?route=checkout/cart';
//				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
//
//				$('.success').fadeIn('slow');
//
//				$('#cart-total').html(json['total']);
//
//				$('html, body').animate({scrollTop: 0}, 'slow');
			}	
		}
	});
}
function addToWishList(product_id) {
	$.ajax({
		url: 'index.php?route=account/wishlist/add',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information').remove();
						
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
				$('.success').fadeIn('slow');
				
				$('#wishlist-total').html(json['total']);
				
				$('html, body').animate({scrollTop: 0}, 'slow');
			}	
		}
	});
}

function addToCompare(product_id) { 
	$.ajax({
		url: 'index.php?route=product/compare/add',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information').remove();
						
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
				$('.success').fadeIn('slow');
				
				$('#compare-total').html(json['total']);
				
				$('html, body').animate({scrollTop: 0}, 'slow'); 
			}	
		}
	});
}
function getSortingParams(){
    var getParams = null;
    $("select option:selected").each(function() {

        var val = $(this).val();        
        if(getParams == null && val != null){
            getParams = val;
        } else {
            if(getParams != null && val != null){
                getParams = "&"+getParams+"="+val;
            }
        }
    });
    return getParams;
}
function dropdown(dp){
    var arrData = getFilterParams();
    var getParams = getSortingParams();
    sendRequest(arrData, getParams);
}
function getFilterParams(){
    var arrData = new Array();
//    $('.checkboxDiv > checkbox:selected').each(function() {
//        var item = {};
//        item.param = $(this).parent().attr('id');
//        item.val = $(this).val();
//        //                    console.log($(this).parent().attr('id'));
//        arrData.push(item);
//    });
    //Iterating over all the check boxes
    $("input:checked").each(function() {
        var item = {};
        item.param = $(this).attr('id');
        item.val = $(this).val();
        arrData.push(item);
    });
    return arrData;
}

function sendRequest(arrData, getParams, categoryId, appendToUrl){
        var url = "index.php?route=module/filters/applyFilter&path="+categoryId;
        
        if(getParams != 0){
            url = url+"&"+getParams;
        }
        if(appendToUrl != null){
            url = url+"&"+appendToUrl;
        }
        $('#filter-load').show();
        $.ajax({
            url: url,
            type: "POST",
            data: {filters : arrData},
            dataType: "html"
        }).done(function( msg ) {
            $('#filter-load').hide()
            $('div').remove('.pagination');
            var productDiv;
            if($('.product-list').length){
                productDiv = '.product-list';
            } else {
                productDiv = '.product-grid';
            }
            $('div').remove(productDiv);
            $('div').remove('.pagination');
            $('div').remove('.buttons');
            $('div').remove('.product-list')
            $(msg).insertAfter('.sort');
        });
    }

    function loadPostCode(divId){

        $.ajax({
		url: 'index.php?route=checkout/onepagecheckout/pincode&pincode=' + $(divId+ ' input[name=\'postcode\']').val(),
		dataType: 'json',
		beforeSend: function() {
			$(divId+' input[name=\'postcode\']').before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},
		success: function(json) {
                    if(!json['error']){
                        if(json['location'] == json['city_name']){
                            var content = 'City: '+json['city_name']+', State: '+json['state'];
                        } else{
                            var content = 'Locality: '+json['location']+', City: '+json['city_name']+', State: '+json['state'];
                        }
//                        $(divId+' input[name=\'city\']').val(json['city_name']);
//                        $(divId+' #city_info').empty();
//                        $(divId+' #city_info').append(content);
//                        $(divId+' #country_id').val(json['country_id']);
//                        $(divId+' #zone_id').val(json['state_id']);
                    } else{

                        //Clearing of pincode and other data on error
//                        $('#guest-buy input[name=\'postcode\']').val('');
                        $(divId+' input[name=\'city\']').val('');
//                        $(divId+' #country_id').val('');
//                        $(divId+' #zone_id').val('');
//                        $(divId+' #city_info').empty();
                        $(divId+' #modal-error').html(json['error']);
                        $('#filter-load').hide();
                        $( "#modal-error" ).dialog({
                            modal: true,                          
                            buttons: {
                                    Ok: function() {
                                        $(divId+ ' input[name=\'postcode\']').val('');
                                            $( this ).dialog( "close" );


                                    }
                            }
                        });
                    }
//
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
    }
