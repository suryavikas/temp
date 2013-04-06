<?php echo $header; ?>
<div id="content">
	<div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
<div class="box">
    <div class="left"></div>
    <div class="right"></div>
    <div class="heading">
	<h1><img src="view/image/feed.png" alt="" /> <?php echo $heading_title; ?></h1>
	<div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">
	<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
	    <table class="form">
		<tr>
		    <td><?php echo $entry_status; ?></td>
		    <td><select name="latest_products_rss_status">
			    <?php if ($latest_products_rss_status && $latest_products_rss_status == 1) {
 ?>
    			    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
    			    <option value="0"><?php echo $text_disabled; ?></option>
<?php } else { ?>
    			    <option value="1"><?php echo $text_enabled; ?></option>
    			    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
<?php } ?>
			</select></td>
		</tr>
		<tr>
		    <td><?php echo $entry_limit; ?></td>
		    <td>
			<input name="latest_products_rss_limit" value="<?php echo $latest_products_rss_limit ? $latest_products_rss_limit : '100'; ?>">
			<?php if ($error_limit) { ?>
    			<span class="error"><?php echo $error_limit; ?></span>
			<?php } ?>
		    </td>
    		</tr>
    		<tr>
    		    <td><?php echo $entry_show_price; ?></td>
    		    <td><input type="checkbox" name="latest_products_rss_show_price" value="1"<?php echo $latest_products_rss_show_price == 1 ? ' checked="checked"' : ''; ?>>
    		    </td>
    		</tr>
			<tr>
    		    <td><?php echo $entry_include_tax; ?></td>
    		    <td><input type="checkbox" name="latest_products_rss_include_tax" value="1"<?php echo $latest_products_rss_include_tax == 1 ? ' checked="checked"' : ''; ?>>
    		    </td>
    		</tr>
    		<tr>
    		    <td><?php echo $entry_show_image; ?></td>
    		    <td><input type="checkbox" name="latest_products_rss_show_image" value="1"<?php echo $latest_products_rss_show_image == 1 ? ' checked="checked"' : ''; ?>>
    		    </td>
    		</tr>
    		<tr>
    		    <td><?php echo $entry_image_size; ?></td>
    		    <td><input type="text" size="3" value="<?php echo $latest_products_rss_image_width; ?>" name="latest_products_rss_image_width">
    			x
    			<input type="text" size="3" value="<?php echo $latest_products_rss_image_height; ?>" name="latest_products_rss_image_height">
			<?php if ($error_image_dimensions) { ?>
    			<span class="error"><?php echo $error_image_dimensions; ?></span>
			<?php } ?>
    		    </td>
    		</tr>
    		<tr>
    		    <td><?php echo $entry_data_feed; ?></td>
    		    <td><textarea cols="40" rows="5" readonly="readonly"><?php echo $data_feed; ?></textarea></td>
    		</tr>
    	    </table>
    	</form>
        </div>
    </div>
</div>
<?php echo $footer; ?>