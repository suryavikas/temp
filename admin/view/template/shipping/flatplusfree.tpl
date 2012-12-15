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
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/shipping.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">
        
     <div class="vtabs"><a href="#tab-general"><?php echo $tab_general; ?></a>
     <a href="#tab-contact"><?php echo $tab_contact; ?></a>
        <?php foreach ($geo_zones as $geo_zone) { ?>
        <a href="#tab-geo-zone<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></a>
        <?php } ?>
      </div>
       <div id="tab-contact" class="htabs-content"><table width="100%" border="0" cellpadding="2">
 		 <tr>
            <td rowspan="2" valign="top"><img src="view/image/logo_scmv.png" title="I'm Happy!" /></td>
            <td>
             <table width="100%" border="0" cellpadding="2">
                  <tr>
                    <td>Email:</td>
                    <td><a href="mailto:yop289@gmail.com">yop289@gmail.com</a></td>
                  </tr>
                  <tr>
                    <td>Current Version:</td>
                    <td><?php echo $current_version; ?> <div id="newversion"></div></td>
                  </tr>
                  <tr>
                    <td></td>
                    <td><div id="what_is_new"></div></td>
                  </tr>
                  <tr>
            <td>OpenCart Url</td>
            <td><a href="http://www.opencart.com/index.php?route=extension/extension/info&amp;extension_id=2730" target="_blank">
      http://www.opencart.com/index.php?route=extension/extension/info&amp;extension_id=2730</a></td>
          </tr>
</table>
<br />
   <br />
    <?php if (isset($version['modules'])){ ?>
    
     <strong>Other modules:</strong><br />
    
	<table width="100%" border="0" cellpadding="2">

  <?php foreach ($version['modules'] as $modules) { ?>
  
  <tr>
          <td height="66"><strong><br />
            <?php echo $modules['name']; ?> - v<?php echo $modules['version']; ?></strong><br />
           <?php echo str_replace("@@@","<br>",$modules['resume']); ?><br />
            Manual: <a href="http://www.ocmodules.com/download/<?php echo $modules['manual']; ?>" target="_blank"><?php echo $modules['manual']; ?></a><br />
            OC: <a href="http://www.opencart.com/index.php?route=extension/extension/info&amp;extension_id=<?php echo $modules['id']; ?>" target="_blank">http://www.opencart.com/index.php?route=extension/extension/info&extension_id=<?php echo $modules['id']; ?>
            </a></td>
          <td>&nbsp;</td>
        </tr>
     
     <?php } ?>
     </table>   
    <?php } ?>    
        
     </td>
		
 <?php 
 if ($version){ 
 
 if ($version['version']!=$current_version){ ?>  
 
       <script>
       $('#contact').append('<img id=\"warning\" src=\"view/image/warning.png\" width=\"15\" height=\"15\" align=\"absmiddle\" hspace=\"10\" border=\"0\" />');  $('#contact').css({'background-color': '#FFD1D1','border': '1px solid #F8ACAC','color': 'red','text-decoration': 'blink'});
	   $('#newversion').append ('<span style=\"color:red\"><strong>New version for this extesion available <?php echo $version['version']; ?></strong></span>');
	   $('#what_is_new').append('<?php echo html_entity_decode(str_replace("@@@","<br>",$version['whats_new']), ENT_QUOTES, 'UTF-8'); ?> ');
      </script>
      <?php } } ?>
 
 
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</div>
   <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-general" class="vtabs-content">
          <table class="form">
            <tr>
              <td><?php echo $entry_status; ?></td>
              <td><select name="flatplusfree_status">
                  <?php if ($flatplusfree_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td><?php echo $entry_sort_order; ?></td>
              <td><input type="text" name="flatplusfree_sort_order" value="<?php echo $flatplusfree_sort_order; ?>" size="1" /></td>
            </tr>
          </table>
        </div>
          
      
    <?php foreach ($geo_zones as $geo_zone) { ?>
        <div id="tab-geo-zone<?php echo $geo_zone['geo_zone_id']; ?>" class="vtabs-content">
       
        <table class="form">
          <tr>
            <td><?php echo $entry_cost; ?></td>
            <td><input type="text" name="flatplusfree_<?php echo $geo_zone['geo_zone_id']; ?>_cost" value="<?php echo ${'flatplusfree_' . $geo_zone['geo_zone_id'] . '_cost'}; ?>" /></td>
          </tr>
          <tr>
            <td><?php echo $entry_tax; ?></td>
            <td><select name="flatplusfree_<?php echo $geo_zone['geo_zone_id']; ?>_tax_class_id">
                <option value="0"><?php echo $text_none; ?></option>
                <?php foreach ($tax_classes as $tax_class) { ?>
                <?php if ($tax_class['tax_class_id'] == ${'flatplusfree_' . $geo_zone['geo_zone_id'] . '_tax_class_id'}) { ?>
                <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
      
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="flatplusfree_<?php echo $geo_zone['geo_zone_id']; ?>_status">
                <?php if (${'flatplusfree_' . $geo_zone['geo_zone_id'] . '_status'}) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
          <td><?php echo $entry_total; ?></td>
            <td><input type="text" name="flatplusfree_<?php echo $geo_zone['geo_zone_id']; ?>_free_total" value="<?php echo ${'flatplusfree_' . $geo_zone['geo_zone_id'].'_free_total'}; ?>" /></td>
            </tr>
        
          <tr>
            <td><?php echo $entry_sort_order; ?></td>
            <td><input type="text" name="flatplusfree_<?php echo $geo_zone['geo_zone_id']; ?>_sort_order" value="<?php echo ${'flatplusfree_' . $geo_zone['geo_zone_id'] . '_sort_order'}; ?>" size="1" /></td>
          </tr>
        </table>
      
        
        </div>
        
       <?php } ?> 
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
       
    </form>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$('.vtabs a').tabs(); 
//--></script> 
<?php echo $footer; ?> 