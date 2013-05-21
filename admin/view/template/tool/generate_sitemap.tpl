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
    <div class="heading">
      <h1><img src="view/image/backup.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#generate_sitemap').submit();" class="button"><?php echo $button_generate; ?></a></div>
    </div>
    <div class="content">
        <form action="<?php echo $generate_sitemap; ?>" method="post" enctype="multipart/form-data" id="generate_sitemap">
         <table class="form">
          <tr>
           <td><?php echo $text_common; ?></td>
          </tr>
          <?php if ($output != '') { ?>
          <tr>
           <td><?php echo $output; ?></td>
          </tr>
          <?php } ?>
         </table>
        </form>
      </div>
    </div>
</div>
<?php echo $footer; ?>