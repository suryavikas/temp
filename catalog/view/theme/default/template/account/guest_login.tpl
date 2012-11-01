<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <h1><?php echo $heading_title; ?></h1>
       <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="login">
        <div class="content">
          <b><?php echo $entry_email; ?></b><br />
          <input type="text" name="email" value="" />
          <br />
          <!--<br />
          <b><?php echo $entry_ip; ?></b><br />
          <input type="text" name="ip" value="" />
          <br />
		  <?php echo $text_ip; ?>
          <br />-->
          <br />
          <b><?php echo $entry_order_number; ?></b><br />
          <input type="text" name="order_number" value="" />
          <br />
		  <?php echo $text_order_number; ?>
          <br />
          <br />
          <br />
          <a onclick="$('#login').submit();" class="button"><span><?php echo $button_continue; ?></span></a>
        </div>
      </form>
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--
$('#login input').keydown(function(e) {
	if (e.keyCode == 13) {
		$('#login').submit();
	}
});
//--></script>   
<?php echo $footer; ?> 