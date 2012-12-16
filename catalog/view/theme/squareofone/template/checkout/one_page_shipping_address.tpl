<h2><?php echo $text_welcome_shipping ?></h2>
<?php if ($addresses) { ?>
    <input type="radio" name="shipping_address" value="existing" id="shipping-address-existing" checked="checked" />
    <label for="shipping-address-existing"><?php echo $text_address_existing; ?></label>
    <div id="shipping-existing">
        <select name="address_id" style="width: 100%; margin-bottom: 15px;" size="5">
            <?php foreach ($addresses as $address) { ?>
                <?php if ($address['address_id'] == $address_id) { ?>
                    <option value="<?php echo $address['address_id']; ?>" selected="selected"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
                <?php } else { ?>
                    <option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
                <?php } ?>
            <?php } ?>
        </select>
    </div>
    <p>
        <input type="radio" name="shipping_address" value="new" id="shipping-address-new" />
        <label for="shipping-address-new"><?php echo $text_address_new; ?></label>
    </p>
<?php } ?>
<div id="shipping-new" style="display: <?php echo ($addresses ? 'none' : 'block'); ?>;">
    <div id="loggedin-user">
        <div id="loggedin-user-new-address-error" style="display:none; border: 1px solid;"></div>
        <fieldset title="<?php echo $entry_email; ?>" id="email" class="form-field-full">
            <label>
                <span>
                    <em>*</em>
                </span>
                <?php echo $entry_email; ?>
            </label>
            <input type="text" name="email" value="" class="form-field-input-full" />
        </fieldset>
        <fieldset title="<?php echo $entry_postcode; ?>" id="pincode" class="form-field-left">
            <label>
                <span>
                    <em>*</em>
                </span>
                <?php echo $entry_postcode; ?>
            </label>
            <input type="text" name="postcode" value="" class="form-field-input-half" />
        </fieldset>
        
        <div id="city_info"></div>
        <input type="hidden" name="country_id" id="country_id"/>
<!--        <input type="hidden" name ="zone_id" id ="zone_id"/>-->
        <input type="hidden" name="shipping_address_guest" value="1" id="shipping" />
        <fieldset title="<?php echo $entry_firstname; ?>" id="first_name" class="form-field-left">
            <label>
                <span>
                    <em>*</em>
                </span>
                <?php echo $entry_firstname; ?>
            </label>
            <input type="text" name="firstname" value="" class="form-field-input-half" />
        </fieldset>
        <fieldset title="<?php echo $entry_lastname; ?>" id="last_name" class="form-field-left">
            <label>
                <span>
                    <em>*</em>
                </span> <?php echo $entry_lastname; ?>
            </label>
            <input type="text" name="lastname" value="" class="form-field-input-half" />
        </fieldset>
        <fieldset title="<?php echo $entry_postcode; ?>" id="pincode" class="form-field-left">
                <label>
                        <span>
                            <em>*</em>
                        </span>
                        <?php echo $entry_city; ?>
                </label>
                <input type="text" name="city"  value="" class="form-field-input-half" />
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

        <fieldset title="<?php echo $entry_telephone; ?>" id="email" class="form-field-full">
            <label>
                <span>
                    <em>*</em>
                </span>
                <?php echo $entry_telephone; ?>
            </label>
            <input type="text" name="telephone" value="" class="form-field-input-full" />
        </fieldset>
        <fieldset title="<?php echo $entry_address_1; ?>" id="email" class="form-field-full">
            <label>
                <span>
                    <em>*</em>
                </span>
                <?php echo $entry_address_1; ?>
            </label>
            <textarea rows="4" cols="34" name="address_1" value="" class="form-field-input-full"> </textarea>

        </fieldset>
        <fieldset title="<?php echo $entry_address_2; ?>" id="email" class="form-field-full">
            <label>
                <span></span>
                <?php echo $entry_address_2; ?>
            </label>
            <input type="text" name="address_2" value="" class="form-field-input-full" />
        </fieldset>

        <div style="display:none;">
            <?php echo $entry_fax; ?><br />
            <input type="text" name="fax" value="" class="large-field" />
            <br />
            <br />
        </div>
        <div style="display:none;">
            <?php echo $entry_company; ?><br />
            <input type="text" name="company" value="" class="large-field" />
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
            <input type="text" name="company_id" value="" class="large-field" />
            <br />
            <br />
        </div>
        <div id="tax-id-display" style="display:none;"><span id="tax-id-required" class="required">*</span> <?php echo $entry_tax_id; ?><br />
            <input type="text" name="tax_id" value="" class="large-field" />
            <br />
            <br />
        </div>
    </div>
</div>
<br />
<div class="buttons">
    <div class="right">
        <input type="button" value="<?php echo $button_continue; ?>" id="button-shipping-address" class="button" />
    </div>
</div>
<script type="text/javascript"><!--
    $('#shipping-address input[name=\'shipping_address\']').live('change', function() {
        if (this.value == 'new') {
            $('#shipping-existing').hide();
            $('#shipping-new').show();
        } else {
            $('#shipping-existing').show();
            $('#shipping-new').hide();
        }
    });
    //--></script>
<script type="text/javascript"><!--
    $('#shipping-address select[name=\'country_id\']').bind('change', function() {
        $.ajax({
            url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
            dataType: 'json',
            beforeSend: function() {
                $('#shipping-address select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
            },
            complete: function() {
                $('.wait').remove();
            },
            success: function(json) {
                if (json['postcode_required'] == '1') {
                    $('#shipping-postcode-required').show();
                } else {
                    $('#shipping-postcode-required').hide();
                }
			
                html = '<option value=""><?php echo $text_select; ?></option>';
			
                if (json['zone'] != '') {
                    for (i = 0; i < json['zone'].length; i++) {
                        html += '<option value="' + json['zone'][i]['zone_id'] + '"';
	    			
                        if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
                            html += ' selected="selected"';
                        }
	
                        html += '>' + json['zone'][i]['name'] + '</option>';
                    }
                } else {
                    html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
                }
			
                $('#shipping-address select[name=\'zone_id\']').html(html);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $('#shipping-address select[name=\'country_id\']').trigger('change');
    //--></script>