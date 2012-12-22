<?php echo $header; ?>
<div id="modal-error" title="<?php echo $text_modal_title ; ?>" style="display:none;">
    <p>
        <span class="ui-icon ui-icon-notice" style="float: left; margin: 0 7px 50px 0;"></span>
        <?php echo $error_no_shipping_to_this_pincode; ?>
    </p>
    <p><br></p>
</div>
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
                            <div id="login-guest-not-selected" style="display:none;" class="error" ></div>
                            <div id="login" class="left">
                                <?php if (!$logged) { ?>
                                    <h2><?php echo $text_returning_customer; ?></h2>
                                    <div id="login-buy-error" style="display:none;" ></div>
                                    <b><?php echo $entry_email; ?></b><br />
                                    <input type="email" name="email" class="large-field" required="required" data-message="<?php echo $error_email; ?>"/>
                                    
                                    <b><?php echo $entry_password; ?></b><br />
                                    <input type="password" name="password" class="large-field" required="required" data-message="<?php echo $error_password_empty; ?>"/>
                                    
                                    <input type="button" value="<?php echo $button_login; ?>" id="button-login" class="button" /><br />
                                    
                                    <?php echo '<span>'.$text_option.'</span>'; ?>
                                    <br />
                                    <div><?php echo $text_welcome; ?>
                                    </div>
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
                                <?php if (!$logged) { ?>
                                <div id="guest-buy">
                                    <h2><?php echo $text_your_details; ?></h2>
                                    
                                    <div id="guest-buy-error" style="display:none"></div>
                                     <fieldset title="<?php echo $entry_email; ?>"  class="form-field-full">
                                         <label>
                                                <span>
                                                    <em>*</em>
                                                </span>
                                                 <?php echo $entry_email; ?>
                                        </label>
                                        <input type="text" id="email" name="email" value="<?php echo $email; ?>" class="form-field-input-full" />
                                        <div id="suggest"></div>
                                     </fieldset>
                                    
                                    <fieldset title="<?php echo $entry_postcode; ?>" id="pincode" class="form-field-left">
                                        <label>
                                                <span>
                                                    <em>*</em>
                                                </span>
                                                <?php echo $entry_postcode; ?>
                                        </label>
                                        <input type="text" maxlength="6" name="postcode" value="<?php /*echo $postcode;*/ ?>" class="form-field-input-half" />
                                        <span>

                                            <span id="city_info"></span>
                                            <input type="hidden" name="country_id" id="country_id" />
<!--                                            <input type="hidden" name ="zone_id" id ="zone_id"/>-->
                                            <input type="hidden" name="shipping_address_guest" value="1" id="shipping" />
                                        </span>
                                    </fieldset>
                                                         
                                    
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
                                    
                                    <fieldset title="<?php echo $entry_postcode; ?>" id="pincode" class="form-field-left">
                                        <label>
                                                <span>
                                                    <em>*</em>
                                                </span>
                                                <?php echo $entry_city; ?>
                                        </label>
                                        <input type="text" name="city"  value="<?php echo $city; ?>" class="form-field-input-half" />
                                    </fieldset>
                                    <fieldset title="<?php echo $text_none; ?>" id="zone" class="form-field-left">
                                        <label>
                                                <span>
                                                    <em>*</em>
                                                </span>
                                                <?php echo $entry_zone; ?>
                                        </label>
                                        <select name="zone_id" class="form-field-input-half-selected">
                                            <option value="0" selected="selected"><?php echo $text_none; ?></option>
                                        <?php
                                                foreach ($zones as $zone) {

                                                    echo '<option value="'.$zone['zone_id'].'" >'. $zone['name'] .'</option>';
                                                }
                                        ?>
                                        </select>

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
                                        <input type="button" value="<?php echo $text_button; ?>" id="button-guest" class="button" />
                                    </div>
                                </div>
                                </div>
                                <?php } else{ ?>
                                <div id="guest-buy">
                                    
                                </div>
                                <?php
                                }
                                ?>

                            </div>
                    </div>                    
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

        <?php echo $content_bottom; ?>
    </div>

    <!--<script src="http://cdn.jquerytools.org/1.2.7/all/jquery.tools.min.js"></script>-->
<script src="catalog/view/javascript/jquery/jquery-tools/jquery.tools.min.js"></script>
    <script type="text/javascript"><!--
        var canProceed = false;
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
                if(index > 0 && !canProceed){
                    $("#login-guest-not-selected").html("Pls choose to login or do a guest checkout");
                    $("#login-guest-not-selected").show();
                    $("#login-guest-not-selected").delay(2000).fadeOut(400)
;
                    return false;
                }
                
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
				<?php }  ?>

                        } else if (json['error']) {
                                $('#login-buy-error').css('display', 'block');
                                $('#login-buy-error').html('<span class="error">' +json['error']['warning']+ '</span>');
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
        });


        //Load city & country based on pincode
    $('#guest-buy input[name=\'postcode\']').bind('change', function() {
	loadPostCode('#guest-buy');
    });
    $('#loggedin-user input[name=\'postcode\']').live('change', function() {
	loadPostCode('#login');
    });
    
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
                        $('loggedin-user-new-address-error').html("");
                        $('loggedin-user-new-address-error').hide();
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
                                var errorList = "";
                                for(x in json['error']){
                                   errorList += '<span class="error">' + json['error'][x] + '</span>' ;

                                }                                
                                $('#loggedin-user-new-address-error').html(errorList);
                                $('#loggedin-user-new-address-error').show();

			} else {
                            if($('#login input[name=\'shipping_address\']:checked').val() == "existing"){
                                $.ajax({
					url: 'index.php?route=checkout/onepagecheckout/loggedin_user_payment_method',
					dataType: 'html',
					success: function(html) {
                                                $('#payment-process').html(html);
                                                canProceed = true;
                                                var api = $("ul.css-tabs").data("tabs");
                                                api.next();
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
                            } else{
                                $('#login').load('index.php?route=checkout/onepagecheckout/loggedin_user_shipping_address');
                            }
                            
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
//        }
	
});


// Guest
$('#button-guest').live('click', function() {
        canProceed = false;
	$.ajax({
                    url: 'index.php?route=checkout/onepagecheckout/set_payment_shipping',
                    type: 'post',
                    data: $('#guest-buy input[type=\'text\'], #guest-buy input[type=\'checkbox\']:checked, #guest-buy select, #guest-buy textarea, #guest-buy input[type=\'hidden\']'),
                    dataType: 'html',
                    success: function(json) {
                        try{
                             result = jQuery.parseJSON(json);
                            if (result.redirect) {
				location = result.redirect;
                            } else if (result.error) {                                
                                var errors = '';
                                if (result.error.email) {
					errors += '<span class="error">' + result.error.email + '</span>';
				}
                                if (result.error.postcode) {
					errors += '<span class="error">' + result.error.postcode + '</span>';
				}
                                if (result.error.firstname) {
					errors += '<span class="error">' + result.error.firstname + '</span>';
				}
				if (result.error.lastname) {
					errors += '<span class="error">' + result.error.lastname + '</span>';
				}
				
				if (result.error.telephone) {
					errors += '<span class="error">' + result.error.telephone + '</span>';
				}
				if (result.error.address_1) {
					errors += '<span class="error">' + result.error.address_1 + '</span>';
				}
				if (result.error.city) {
					errors += '<span class="error">' + result.error.city + '</span>';
				}
				
				if (result.error.country) {
					errors += '<span class="error">' + result.error.country + '</span>';
				}
				if (result.error.zone) {
					errors += '<span class="error">' + result.error.zone + '</span>';
				}
                                $('#guest-buy-error').html(errors);
                                $('#guest-buy-error').show();
                            }
                        } catch(e){
                            $('#guest-buy-error').html('');
                            $('#guest-buy-error').hide();
                            $('#payment-process').html(json);
                            canProceed = true;
                            var api = $("ul.css-tabs").data("tabs");
                            api.next();
                        }

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
        //--></script>
            <style type="text/css">
.all_page_left h1{
    float: none!important;
    width: auto!important;
        font-size:22px;
}
.all_page_left {
    border: 1px solid #CCCCCC;
    padding: 5px!important;

}
.all_page_right {
    width: 625px!important;
}
.all_page_right h2{
    font-size: 18px;
/*    padding: 3px 0 6px 10px;*/
}
    </style>
    <style type="text/css">

.all_page_left{
width:350px !important;
}
.mini-cart-info .image img {
width:90px;
height:90px;
}
.mini-cart-info .name{
font:normal 11px Arial, Helvetica, sans-serif;
}
.mini-cart-info .name a{
font:normal 11px Arial, Helvetica, sans-serif;
}
</style>
        </div>
    <?php echo $footer; ?>