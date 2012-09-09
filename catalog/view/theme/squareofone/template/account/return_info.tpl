<?php echo $header; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>  <div class="all_page_left">
  <?php echo $column_left; ?><?php echo $column_right; ?>
    </div>
  
    <div class="all_page_right">
  
  <table class="list">
    <thead>
      <tr>
        <td class="left" colspan="2"><?php echo $text_return_detail; ?></td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="left" style="width: 50%;"><b><?php echo $text_return_id; ?></b> #<?php echo $return_id; ?><br />
          <b><?php echo $text_date_added; ?></b> <?php echo $date_added; ?></td>
        <td class="left" style="width: 50%;"><b><?php echo $text_order_id; ?></b> #<?php echo $order_id; ?><br />
          <b><?php echo $text_date_ordered; ?></b> <?php echo $date_ordered; ?></td>
      </tr>
    </tbody>
  </table>
  <h2><?php echo $text_product; ?></h2>
   <div style="border: 1px solid #CCCCCC; margin:-5px 0 15px;padding:10px; display:block;">
  <table class="list">
    <thead>
      <tr>
        <td class="left" style="width: 33.3%;"><?php echo $column_product; ?></td>
        <td class="left" style="width: 33.3%;"><?php echo $column_model; ?></td>
        <td class="right" style="width: 33.3%;"><?php echo $column_quantity; ?></td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="left"><?php echo $product; ?></td>
        <td class="left"><?php echo $model; ?></td>
        <td class="right"><?php echo $quantity; ?></td>
      </tr>
    </tbody>
  </table>
  <table class="list">
    <thead>
      <tr>
        <td class="left" style="width: 33.3%;"><?php echo $column_reason; ?></td>
        <td class="left" style="width: 33.3%;"><?php echo $column_opened; ?></td>
        <td class="left" style="width: 33.3%;"><?php echo $column_action; ?></td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="left"><?php echo $reason; ?></td>
        <td class="left"><?php echo $opened; ?></td>
        <td class="left"><?php echo $action; ?></td>
      </tr>
    </tbody>
  </table>
  <table class="list">
    <?php if ($comment) { ?>
    <thead>
      <tr>
        <td class="left"><?php echo $text_comment; ?></td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="left"><?php echo $comment; ?></td>
      </tr>
    </tbody>
  </table>
    <div class="clear"></div>
      </div>
  <?php } ?>
  <?php if ($histories) { ?>
  <h2><?php echo $text_history; ?></h2>
     <div style="border: 1px solid #CCCCCC; margin:-5px 0 15px;padding:10px; display:block;">
  <table class="list">
    <thead>
      <tr>
        <td class="left" style="width: 33.3%;"><?php echo $column_date_added; ?></td>
        <td class="left" style="width: 33.3%;"><?php echo $column_status; ?></td>
        <td class="left" style="width: 33.3%;"><?php echo $column_comment; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($histories as $history) { ?>
      <tr>
        <td class="left"><?php echo $history['date_added']; ?></td>
        <td class="left"><?php echo $history['status']; ?></td>
        <td class="left"><?php echo $history['comment']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
      <div class="clear"></div>
      </div>
  <?php } ?>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
  </div>
  
  </div>
  
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>