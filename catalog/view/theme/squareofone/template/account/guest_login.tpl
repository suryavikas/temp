<?php echo $header; ?>
<div id="content"><?php echo $content_top; ?>
    <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
    <div class="all_page_left">
  <?php echo $column_left; ?><?php echo $column_right; ?>
    </div>
     <div class="all_page_right">
  <h2><?php echo $heading_title; ?></h2>
       <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="guest-tracking">
        <div class="content">
          <b><?php echo $entry_email; ?></b><br />
          <input type="text" name="email" value="" class="inpt"/>
          <br />
          <!--<br />
          <b><?php echo $entry_ip; ?></b><br />
          <input type="text" name="ip" value="" />
          <br />
		  <?php echo $text_ip; ?>
          <br />-->
          <br />
          <b><?php echo $entry_order_number; ?></b><br />
          <input type="text" name="order_number" value="" class="inpt"/>
          <br />
		  <?php echo $text_order_number; ?>
          <br />
          <br />
          <div class="checkout">
          <a onclick="$('#guest-tracking').submit();" class="button"><?php echo $button_continue; ?></a>
          </div>
        </div>
      </form>
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