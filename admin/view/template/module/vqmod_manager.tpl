<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
    <h1><img src="view/image/module.png" alt="<?php echo $heading_title; ?>" /><?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
    <?php if ($path_set == TRUE) { ?>
    <!-- VQMods -->
    <h4 style="font-size:15px; background-color:#EEEEEE; padding:9px 0px 9px 40px; border:solid 1px #B6B8D3; background-image:url('view/image/product.png'); background-repeat:no-repeat; background-position:1% 50%;"><?php echo $heading_vqmods; ?></h4>
    <div>
      <table class="list">
        <thead>
          <tr>
            <td class="left"><?php echo $column_file_name; ?><hr style="color:#ddd; background-color:#ddd; height:1px; border:none; text-align:left; width:97%" /><i><?php echo $column_id; ?></i></td>
            <td class="center"><?php echo $column_version; ?></td>
            <td class="center"><?php echo $column_vqmver; ?></td>
            <td class="center"><?php echo $column_author; ?></td>
            <td class="center"><?php echo $column_status; ?></td>
            <td class="center"><?php echo $column_action; ?></td>
            <td class="center"><?php echo $column_delete; ?></td>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($vqmods)) { ?>
          <?php foreach ($vqmods as $vqmod) { ?>
          <tr>
            <td class="left"><strong><?php echo $vqmod['file_name']; ?></strong><br /><div style="font-size:0.9em; margin:3px 0px;"><?php echo $vqmod['id']; ?></div></td>
            <td class="center"><?php echo $vqmod['version']; ?></td>
            <td class="center"><?php echo $vqmod['vqmver']; ?></td>
            <td class="center"><?php echo $vqmod['author']; ?></td>
            <td class="center"><?php echo $vqmod['status'] ?></td>
            <td class="center" style="white-space:nowrap;"><?php foreach ($vqmod['action'] as $action) { ?>
              [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
              <?php } ?></td>
            <td class="center" style="white-space:nowrap;">
              [ <a href="<?php echo $vqmod['delete'] ?>"><?php echo $text_delete ?></a> ]</td>
          </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td class="center" colspan="6"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <!-- VQMOD Upload -->
      <table class="form">
        <tr>
          <td><?php echo $entry_upload; ?>&nbsp;<input type="file" name="vqmod_file" /><input type="submit" name="upload" value="Upload" /></td>
        </tr>
      </table>
      <!-- /VQMOD Upload -->
    </div>
    <!-- /VQMods -->
    <?php } ?>

    <!-- Settings -->
    <h4 style="font-size:15px; background-color:#EEEEEE; padding:9px 0px 9px 40px; border:solid 1px #B6B8D3; background-image:url('view/image/setting.png'); background-repeat:no-repeat; background-position:1% 50%;"><?php echo $heading_settings; ?></h4>
    <div>
      <table class="form">
         <tr>
           <td>
             <span class="required">*</span> <?php echo $entry_vqmod_path; ?>
             <?php if ($path_set == FALSE) { ?>
               <br /><span class="help" style="padding-top:3px;"><?php echo $text_autodetect; ?></span>
             <?php } else { ?>
               <br /><span class="help" style="padding-top:3px;"><?php echo $text_vqmod_path; ?></span>
             <?php } ?>
           </td>
           <td>
             <input type="text" name="vqmod_path" value="<?php echo $vqmod_path; ?>" size="56" />
           </td>
         </tr>
         <?php if ($path_set == TRUE) { ?>
         <tr>
           <td><?php echo $entry_vqcache; ?><br /><span class="help" style="padding-top:3px;"><?php echo $text_vqcache_help; ?></span></td>
           <td style="border-top-color:#fff;">
             <?php if (isset($vqcache)) { ?>
             <select multiple="multiple" size="7" style="min-width:200px;">
               <?php foreach ($vqcache as $vqcache_file) { ?>
                <option><?php echo $vqcache_file; ?></option>
               <?php } ?>
             </select><br />
             <?php } ?>
             <a href="<?php echo $clear_vqcache; ?>" class="button" style="margin-top:3px;"><span><?php echo $button_clear; ?></span></a>
           </td>
         </tr>
         <tr>
           <td><?php echo $entry_backup; ?></td>
           <td><a href="<?php echo $backup; ?>" class="button" style="margin-top:3px;"><span><?php echo $button_backup; ?></span></a></td>
         </tr>
         <?php } ?>
         <tr>
           <td><?php echo $entry_ext_version; ?></td>
           <td><?php echo $vqmod_manager_version; ?></td>
         </tr>
      </table>
    </div>
    <!-- /Settings -->

    <!-- Error Log -->
    <?php if ($path_set == TRUE) { ?>
    <h4 style="font-size:15px; background-color:#EEEEEE; padding:9px 0px 9px 40px; border:solid 1px #B6B8D3; background-image:url('view/image/log.png'); background-repeat:no-repeat; background-position:1% 50%;"><?php echo $heading_error_log; ?></h4>
    <div>
      <table class="form">
        <tr>
          <td style="border-bottom-color:#fff;"><textarea rows="20" cols="160" style="width: 99%; height: 300px; padding: 5px; border: 1px solid #CCCCCC; background: #FFFFFF; overflow: scroll;"><?php echo $log; ?></textarea></td>
        </tr>
        <tr>
           <td style="border-top-color:#fff;"><div style="text-align:right;"><a href="<?php echo $clear_log; ?>" class="button"><span><?php echo $button_clear; ?></span></a></div></td>
        </tr>
      </table>
    </div>
    <?php } ?>
    <!-- /Error Log -->
    </form>
  </div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	// Confirm Delete
	$('a').click(function(){
		if ($(this).attr('href') != null && $(this).attr('href').indexOf('delete',1) != -1) {
			if (!confirm ('<?php echo $warning_vqmod_delete; ?>')) {
				return false;
			}
		}
	});

	// Confirm vqmod_opencart.xml Uninstall
	$('a').click(function(){
		if ($(this).attr('href') != null && $(this).attr('href').indexOf('vqmod_opencart',1) != -1 && $(this).attr('href').indexOf('uninstall',1) != -1) {
			if (!confirm ('<?php echo $warning_required_uninstall; ?>')) {
				return false;
			}
		}
	});

	// Confirm vqmod_opencart.xml Delete
	$('a').click(function(){
		if ($(this).attr('href') != null && $(this).attr('href').indexOf('vqmod_opencart',1) != -1 && $(this).attr('href').indexOf('delete',1) != -1) {
			if (!confirm ('<?php echo $warning_required_delete; ?>')) {
				return false;
			}
		}
	});
});
</script>
<?php echo $footer; ?>