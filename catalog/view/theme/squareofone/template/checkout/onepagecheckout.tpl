<?php echo $header; ?>
<div id="content"><?php echo $content_top; ?>
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <h1><?php echo $heading_title; ?></h1>

    <div class="all_page_left">
        <?php echo $column_left; ?><?php echo $column_right; ?>
    </div>


    <div class="all_page_right">
        <div id="checkout-wizard">
            <div class="cart-header">
                <ul class="css-tabs">
                    <li><a href="#" class="w2">Email & Login</a></li>
                    <li><a href="#" class="w2">Payment</a></li>                    
                </ul>

            </div>
            <div class="css-panes">
                <div style="display: block;">
                    <div class="checkout-content user-checkout-choice">
                        
                            <div id="login" class="left">
                                <?php if (!$logged) { ?>
                                    <h2><?php echo $text_returning_customer; ?></h2>
                                    <div id="login-buy-error" style="display:none;" ></div>
                                    <b><?php echo $entry_email; ?></b><br />
                                    <input type="email" name="email" class="large-field" required="required" data-message="<?php echo $error_email; ?>"/>
                                    <br />
                                    <br />
                                    <b><?php echo $entry_password; ?></b><br />
                                    <input type="password" name="password" class="large-field" required="required" data-message="<?php echo $error_password_empty; ?>"/>
                                    <br />
                                    <a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a><br />
                                    <br />
                                    <input type="button" value="<?php echo $button_login; ?>" id="button-login" class="button" /><br />
                                    <br />
                                <?php
                                } else {
                                ?>
                                    <script type="text/javascript">
                                        $('#login').load('index.php?route=checkout/onepagecheckout/loggedin_user_shipping_address');
                                    </script>
                                <?php } ?>
                            </div>
                         
                       
                            <div class="right">
                                <div id="guest-buy">
                                    <h2><?php echo $text_your_details; ?></h2>
                                    <div id="guest-buy-error" style="display:none"></div>
                                     <fieldset title="<?php echo $entry_email; ?>" id="email" class="form-field-full">
                                         <label>
                                                <span>
                                                    <em>*</em>
                                                </span>
                                                 <?php echo $entry_email; ?>
                                        </label>
                                        <input type="text" name="email" value="<?php echo $email; ?>" class="form-field-input-full" />
                                     </fieldset>                                    
                                    <fieldset title="<?php echo $entry_postcode; ?>" id="pincode" class="form-field-left">
                                        <label>
                                                <span>
                                                    <em>*</em>
                                                </span>
                                                 <?php echo $entry_postcode; ?>
                                        </label>
                                                <input type="text" name="postcode" value="<?php /*echo $postcode;*/ ?>" class="form-field-input-half" />
                                    </fieldset>
                                    <fieldset title="<?php echo $entry_city; ?>" id="<?php echo $entry_city; ?>" class="form-field-left">
                                        <label>
                                                <span>                                                    
                                                </span> <?php echo $entry_city; ?>
                                        </label>
                                        <input type="text" name="city" disabled="true" value="<?php echo $city; ?>" class="form-field-input-half" />
                                    </fieldset>
                                    <div id="city_info"></div>
                                    <input type="hidden" name="country_id" id="country_id"/>
                                    <input type="hidden" name ="zone_id" id ="zone_id"/>
                                    <input type="hidden" name="shipping_address_guest" value="1" id="shipping" />
                                    <fieldset title="<?php echo $entry_firstname; ?>" id="first_name" class="form-field-left">
                                        <label>
                                                <span>
                                                    <em>*</em>
                                                </span>
                                                 <?php echo $entry_firstname; ?>
                                        </label>
                                                <input type="text" name="firstname" value="<?php echo $firstname; ?>" class="form-field-input-half" />
                                    </fieldset>
                                    <fieldset title="<?php echo $entry_lastname; ?>" id="last_name" class="form-field-left">
                                        <label>
                                                <span>
                                                    <em>*</em>
                                                </span> <?php echo $entry_lastname; ?>
                                        </label>
                                        <input type="text" name="lastname" value="<?php echo $lastname; ?>" class="form-field-input-half" />
                                    </fieldset>

                                    <fieldset title="<?php echo $entry_telephone; ?>" id="email" class="form-field-full">
                                         <label>
                                                <span>
                                                    <em>*</em>
                                                </span>
                                                 <?php echo $entry_telephone; ?>
                                        </label>
                                        <input type="text" name="telephone" value="<?php echo $telephone; ?>" class="form-field-input-full" />
                                     </fieldset>
                                    <fieldset title="<?php echo $entry_address_1; ?>" id="email" class="form-field-full">
                                         <label>
                                                <span>
                                                    <em>*</em>
                                                </span>
                                                 <?php echo $entry_address_1; ?>
                                        </label>
                                         <textarea rows="4" cols="34" name="address_1" value="<?php echo $address_1; ?>" class="form-field-input-full"> </textarea>
                                        
                                     </fieldset>
                                     <fieldset title="<?php echo $entry_address_2; ?>" id="email" class="form-field-full">
                                         <label>
                                                <span></span>
                                                 <?php echo $entry_address_2; ?>
                                        </label>
                                        <input type="text" name="address_2" value="<?php echo $address_2; ?>" class="form-field-input-full" />
                                     </fieldset>
                                     
                                    <div style="display:none;">
                                        <?php echo $entry_fax; ?><br />
                                        <input type="text" name="fax" value="<?php echo $fax; ?>" class="large-field" />
                                        <br />
                                        <br />
                                    </div>
                                    <div style="display:none;">
                                        <?php echo $entry_company; ?><br />
                                        <input type="text" name="company" value="<?php echo $company; ?>" class="large-field" />
                                        <br />
                                        <br />
                                    </div>  

                                    <div style="display: <?php echo (count($customer_groups) > 1 ? 'table-row' : 'none'); ?>;"> <?php echo $entry_customer_group; ?><br />
                                        <?php foreach ($customer_groups as $customer_group) { ?>
                                            <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
                                                <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" checked="checked" />
                                                <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
                                                <br />
                                            <?php } else { ?>
                                                <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" />
                                                <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
                                                <br />
                                            <?php } ?>
                                        <?php } ?>
                                        <br />
                                    </div>
                                    <div id="company-id-display" style="display:none;"><span id="company-id-required" class="required">*</span> <?php echo $entry_company_id; ?><br />
                                        <input type="text" name="company_id" value="<?php echo $company_id; ?>" class="large-field" />
                                        <br />
                                        <br />
                                    </div>
                                    <div id="tax-id-display" style="display:none;"><span id="tax-id-required" class="required">*</span> <?php echo $entry_tax_id; ?><br />
                                        <input type="text" name="tax_id" value="<?php echo $tax_id; ?>" class="large-field" />
                                        <br />
                                        <br />
                                    </div>
                                   
                                
                                
                                <div class="buttons">
                                    <div class="right">
                                        <input type="button" value="<?php echo $button_continue; ?>" id="button-guest" class="button" />
                                    </div>
                                </div>
                                </div>
                            </div>
                       

                    </div>
                    <p>
                        <button class="next">Next »</button>
                    </p>
                </div>
                <div style="display: none;" id="payment-process"> 
                    <p>
                        <button class="prev">Prev »</button>
                        <button class="next">Next »</button>
                    </p>
                    
                </div>
                
                
            </div>
            <div id="payment-gateway-info" ></div>
        </div>

        <?php echo $content_bottom; ?></div>
    <script type="text/javascript"><!--
        $(function() {
            // get container for the wizard and initialize its exposing
            //var wizard = $("#checkout-wizard").expose({color: '#789', lazy: true});
            //
            //    // enable exposing on the wizard
            //    wizard.click(function() {
            //        $(this).overlay().load();
            //    });

            // enable tabs that are contained within the wizard
            $("ul.css-tabs").tabs("div.css-panes > div", function(event, index) {

                /* now we are inside the onBeforeClick event */

                // ensure that the "terms" checkbox is checked.
                //                var terms = $("#terms");
                //                if (index > 0 && !terms.get(0).checked)  {
                //                    terms.parent().addClass("error");
                //
                //                    // when false is returned, the user cannot advance to the next tab
                //                    return false;
                //                }
                //
                //                // everything is ok. remove possible red highlight from the terms
                //                terms.parent().removeClass("error");
            });

            // get handle to the tabs API
            var api = $("ul.css-tabs").data("tabs");
            
            // "next tab" button
            $("button.next").click(function() {
                api.next();
            });

            // "previous tab" button
            $("button.prev").click(function() {
                api.prev();
            });
        });
        $('#login input[name=\'shipping_address\']').live('change', function() {
            if (this.value == 'new') {
                    $('#shipping-existing').hide();
                    $('#shipping-new').show();
            } else {
                    $('#shipping-existing').show();
                    $('#shipping-new').hide();
            }
        });
        // Login
        $('#button-login').live('click', function() {
            $('#login-buy-error').html('');
            var inputs = $('.css-panes #login :input').validator({
                    position: 'top left',
                    offset: [-12, 0],
                    message: '<div><em/></div>', // em element is the arrow
                    container: '#login-buy-error',
                    // do not validate inputs when they are edited
                    errorInputEvent: null
            });
            if(inputs.data("validator").checkValidity()){
                $.ajax({
                    url: 'index.php?route=checkout/login/validate',
                    type: 'post',
                    data: $('.css-panes #login :input'),
                    dataType: 'json',
                    beforeSend: function() {
                        $('#button-login').attr('disabled', true);
                        $('#button-login').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
                    },
                    complete: function() {
                        $('#button-login').attr('disabled', false);
                        $('.wait').remove();
                    },
                    success: function(json) {
                        $('.warning, .error').remove();
                        
                        if (json['redirect']) {
                            var api = $("ul.css-tabs").data("tabs");
                           <?php if ($shipping_required) { ?>
				$.ajax({
					url: 'index.php?route=checkout/onepagecheckout/loggedin_user_shipping_address',
					dataType: 'html',
					success: function(html) {
						$('#login').html(html);

					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
				<?php } else { ?>
//				$.ajax({
//					url: 'index.php?route=checkout/onepagecheckout/loggedin_user_payment_method',
//					dataType: 'html',
//					success: function(html) {
//						$('#payment-method .checkout-content').html(html);
//
//						$('#payment-address .checkout-content').slideUp('slow');
//
//						$('#payment-method .checkout-content').slideDown('slow');
//
//						$('#payment-address .checkout-heading a').remove();
//						$('#payment-method .checkout-heading a').remove();
//
//						$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
//					},
//					error: function(xhr, ajaxOptions, thrownError) {
//						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
//					}
//				});
				<?php } ?>

//				$.ajax({
//					url: 'index.php?route=checkout/payment_address',
//					dataType: 'html',
//					success: function(html) {
//						$('#payment-address .checkout-content').html(html);
//					},
//					error: function(xhr, ajaxOptions, thrownError) {
//						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
//					}
//				});
//                            api.next();
//
//                            location = json['redirect'];
                            //                        $('.checkout-content').html('<?php echo $text_login_success; ?>');
                        
                        } else if (json['error']) {
                                $('#login-buy-error').css('display', 'block');
                                $('#login-buy-error').html(json['error']['warning']);
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            } else{
                return false;
            }
            
        });
        

        //Load city & country based on pincode
    $('#guest-buy input[name=\'postcode\']').bind('change', function() {
	loadPostCode('#guest-buy');
    });
    $('#loggedin-user input[name=\'postcode\']').live('change', function() {
	loadPostCode('#login');
    });
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
                            var content = json['city_name']+', '+json['state'];
                        } else{
                            var content = json['location']+', '+json['city_name']+', '+json['state'];
                        }
                        $(divId+' input[name=\'city\']').val(json['city_name']);
                        $(divId+' #city_info').empty();
                        $(divId+' #city_info').append(content);
                        $(divId+' #country_id').val(json['country_id']);
                        $(divId+' #zone_id').val(json['state_id']);
                    } else{

                        //Clearing of pincode and other data on error
//                        $('#guest-buy input[name=\'postcode\']').val('');
                        $(divId+' input[name=\'city\']').val('');
                        $(divId+' #country_id').val('');
                        $(divId+' #zone_id').val('');
                        $(divId+' #city_info').empty();
                        $(divId+' #guest-buy-error').html(json['error']);
                        $( "#guest-buy-error" ).dialog({
                            modal: true,
                            buttons: {
                                    Ok: function() {
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

// Shipping Address			
$('#button-shipping-address').live('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/onepagecheckout/loggedin_user_shipping_address_validate',
		type: 'post',
		data: $('#login input[type=\'text\'], #login input[type=\'hidden\'], #login textarea, #login input[type=\'password\'], #login input[type=\'checkbox\']:checked, #login input[type=\'radio\']:checked, #login select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping-address').attr('disabled', true);
			$('#button-shipping-address').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('#button-shipping-address').attr('disabled', false);
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#login .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					$('.warning').fadeIn('slow');
				}
								
				if (json['error']['firstname']) {
					$('#login input[name=\'firstname\']').after('<span class="error">' + json['error']['firstname'] + '</span>');
				}
				
				if (json['error']['lastname']) {
					$('#login input[name=\'lastname\']').after('<span class="error">' + json['error']['lastname'] + '</span>');
				}	
				
				if (json['error']['email']) {
					$('#login input[name=\'email\']').after('<span class="error">' + json['error']['email'] + '</span>');
				}
				
				if (json['error']['telephone']) {
					$('#login input[name=\'telephone\']').after('<span class="error">' + json['error']['telephone'] + '</span>');
				}		
										
				if (json['error']['address_1']) {
					$('#login input[name=\'address_1\']').after('<span class="error">' + json['error']['address_1'] + '</span>');
				}	
				
				if (json['error']['city']) {
					$('#login input[name=\'city\']').after('<span class="error">' + json['error']['city'] + '</span>');
				}	
				
				if (json['error']['postcode']) {
					$('#login input[name=\'postcode\']').after('<span class="error">' + json['error']['postcode'] + '</span>');
				}	
				
				if (json['error']['country']) {
					$('#login select[name=\'country_id\']').after('<span class="error">' + json['error']['country'] + '</span>');
				}	
				
				if (json['error']['zone']) {
					$('#login select[name=\'zone_id\']').after('<span class="error">' + json['error']['zone'] + '</span>');
				}
			} else {
                           
//                            alert('All is well');
                            $.ajax({
					url: 'index.php?route=checkout/onepagecheckout/loggedin_user_payment_method',
					dataType: 'html',
					success: function(html) {
                                                $('#payment-process').html(html);
                                                var api = $("ul.css-tabs").data("tabs");
                                                api.next();
//						$('#payment-method .checkout-content').html(html);
//
//						$('#payment-address .checkout-content').slideUp('slow');
//
//						$('#payment-method .checkout-content').slideDown('slow');
//
//						$('#payment-address .checkout-heading a').remove();
//						$('#payment-method .checkout-heading a').remove();
//
//						$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
//				$.ajax({
//					url: 'index.php?route=checkout/shipping_method',
//					dataType: 'html',
//					success: function(html) {
//						$('#shipping-method .checkout-content').html(html);
//
//						$('#login .checkout-content').slideUp('slow');
//
//						$('#shipping-method .checkout-content').slideDown('slow');
//
//						$('#login .checkout-heading a').remove();
//						$('#shipping-method .checkout-heading a').remove();
//						$('#payment-method .checkout-heading a').remove();
//
//						$('#login .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
//
//						$.ajax({
//							url: 'index.php?route=checkout/shipping_address',
//							dataType: 'html',
//							success: function(html) {
//								$('#login .checkout-content').html(html);
//							},
//							error: function(xhr, ajaxOptions, thrownError) {
//								alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
//							}
//						});
//					},
//					error: function(xhr, ajaxOptions, thrownError) {
//						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
//					}
//				});
			}  
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
});


// Guest
$('#button-guest').live('click', function() {
	$.ajax({
                    url: 'index.php?route=checkout/onepagecheckout/set_payment_shipping',
                    type: 'post',
                    data: $('#guest-buy input[type=\'text\'], #guest-buy input[type=\'checkbox\']:checked, #guest-buy select, #guest-buy textarea, #guest-buy input[type=\'hidden\']'),
//                    dataType: 'json',
                    dataType: 'html',
                    success: function(html) {
                        $('#payment-process').html(html);
                        var api = $("ul.css-tabs").data("tabs");
                        api.next();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
});
//Payment button click 
 $('#payment-process input[name=\'payment_method\']').live('change', function() {

         $.ajax({
		url: 'index.php?route=checkout/onepagecheckout/validate_payment',
		type: 'post',
		data: $('#payment-process input[type=\'radio\']:checked, #payment-process input[type=\'checkbox\']:checked, #payment-process textarea'),
		dataType: 'json',		
		complete: function() {			
			$('.wait').remove();
		},
		success: function(json) {
			$('.warning, .error').remove();

			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#payment-method .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');

					$('.warning').fadeIn('slow');
				}
			} else {                        
				$.ajax({
					url: 'index.php?route=checkout/onepagecheckout/do_payment',
					dataType: 'html',
					success: function(html) {
                                            $('#payment-gateway-info').html('');
                                            $('#payment-gateway-info').append(html);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
//$('#button-payment-method').live('click', function() {
//$('#payment').submit();
//});

        //--></script>
    <?php echo $footer; ?>