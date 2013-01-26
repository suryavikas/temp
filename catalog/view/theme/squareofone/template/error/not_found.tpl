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
  
  <div class="content"><?php echo $text_error; ?></div>
  <div>Please check our <a href="http://www.squareofone.com/sitemap">sitemap</a>. </div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
  </div>
  </div>
  
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>