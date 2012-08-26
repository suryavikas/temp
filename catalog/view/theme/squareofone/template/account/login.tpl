<?php echo $header; ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>

<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  
  
  <div class="acnt_page_left_part">
  <div style="width:225px; float:left;">
  <?php echo $column_left; ?>
  <?php echo $column_right; ?>
  </div>
  <div class="login_page_left1" >
      <h2><?php echo $text_returning_customer; ?></h2>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
        <div class="content">
        <div class="or_img"></div>
        <div class="rt_cst">
          <p><strong><?php echo $text_i_am_returning_customer; ?></strong></p>
          <b><?php echo $entry_email; ?></b> <div class="clear"></div>
          <input type="text" name="email" value="" class="txt_field" />
         <div class="clear"></div>
         
          <b><?php echo $entry_password; ?></b> <div class="clear"></div>
          <input type="password" name="password" value="" class="txt_field" style="margin-bottom:10px;" />
          <div class="clear"></div>
          <a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>
           <div class="clear"></div>
          <input type="submit" value="<?php echo $button_login; ?>" class="button" style="float:right" />
          <?php if ($redirect) { ?>
          <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
          <?php } ?>
           <div class="clear"></div>
          </div>
           <div class="clear"></div>
        </div>
      </form>
    </div>
  </div>
  
  <div class="acnt_page_right_part">
 
  <div class="ac_lgn">
    <div>
     <h3><?php echo $heading_title; ?></h3>
      <h4><?php echo $text_new_customer; ?></h4>
      <div class="content">
        <p><b><?php echo $text_register; ?></b></p>
        <p><?php echo $text_register_account; ?></p>
        <a href="<?php echo $register; ?>" class="button" style="float:right"><?php echo $button_continue; ?></a></div>
    </div>
    
  </div>
  </div>
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--
$('#login input').keydown(function(e) {
	if (e.keyCode == 13) {
		$('#login').submit();
	}
});
//--></script> 
<?php echo $footer; ?>