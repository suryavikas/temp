<?php echo $header; ?>
<div id="filter-load" style="display: none; position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #000;
    filter:alpha(opacity=50);
    -moz-opacity:0.5;
    -khtml-opacity: 0.5;
    opacity: 0.5;
    z-index: 10000;">
    <img alt="Shopping cart loading..." src="catalog/view/theme/squareofone/image/ajax-loader-filters.gif"  style="position: absolute;
    top: 50%;
    left: 50%;
    width: 100px;
    height: 100px;
    text-align: center;"/>
</div>

<div  id="content"><?php echo $content_top; ?>
  <div class="shop_by"> We found <?php echo $product_total ?> Products for <?php echo $heading_title; ?>
<div class="clear"></div>
</div>
   
   
     <div class="all_page_left"><?php echo $column_left; ?><?php echo $column_right; ?> </div>

<div class="all_page_right">

<div>
  <div class="prd_nm">

    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div> 
  <?php if ($thumb || $description) { ?>
  <div class="category-info">
    <?php if ($thumb) { ?>
    <div class="image"><img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" /></div>
    <?php } ?>
    <?php if ($description) { ?>
    <?php echo $description; ?>
    <?php } ?>
  </div>
  <?php } ?>
 
  <?php if ($products) { ?>
   
  
<!--  <div class="product-filter">
    <div class="display"><b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display('grid');"><?php echo $text_grid; ?></a></div>
    <div class="limit"><b><?php echo $text_limit; ?></b>
      <select onchange="location = this.value;">
        <?php foreach ($limits as $limits) { ?>
        <?php if ($limits['value'] == $limit) { ?>
        <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>-->

  </div>
    <div class="sort prd_sort"><b><?php echo $text_sort; ?></b>
        <?php foreach ($sorts as $sorts) { ?>
        <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
        <a class="product-sorting" href="<?php echo $sorts['href']; ?>" ><?php echo $sorts['text']; ?></a>
        <?php } else { ?>
        <a class="product-sorting" href="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></a>
        <?php } ?>
        <?php } ?>

    </div>
    <div class="pagination category-listing"><?php echo $pagination;
        if($page == 'show'){ ?>
        <span class="links" style="padding-left: 2px">
            <a  href="<?php echo $show_all; ?>"><?php echo $text_show_all ?></a>
        </span>
<?php } ?>
    </div>
    
<!--  <div class="product-compare"><a href="<?php echo $compare; ?>" id="compare-total"><?php echo $text_compare; ?></a></div>-->
  <div class="found_prd_item product-list">
      <ul>
    <?php foreach ($products as $product) {  ?>
    <li>
      <?php if($product['special']){ ?>
            <div class="discount_tag"><?php echo $product['discount_percentage']; ?>%<br>OFF</div>
      <?php }
      ?>
      <?php if ($product['thumb']) { ?>
            <a href="<?php echo $product['href']; ?>">
                <img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
            </a>
      <?php } ?>
      <div class="clear"></div>
      <div class="buy_detail5">
           <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a><br>
            <div class="prc">
                <?php
                if($product['special']){ ?>
                    <div class="prc_left">Price : </div><div class="prc_right"><span><?php echo $product['price']; ?></span>   <?php echo $product['special']; ?></div>
                <?php
                } else {
                ?>
                    <div class="prc_left">Price : </div><div class="prc_right"><?php echo $product['price']; ?></div>
                <?php
                }
                ?>
            
            <div class="clear"></div>
            </div>
            <div class="clear"></div>
<!--            <a class="buy_now_button" onclick="addToCart('<?php echo $product['product_id']; ?>');"><span>more</span></a>-->
            <div class="clear"></div>
    </div>

    </li>
    <?php } ?>
        </ul>
  </div>
  <div class="pagination category-listing"><?php echo $pagination; ?>
    <span class="links" style="padding-left: 2px">
        <a  href="<?php echo $show_all; ?>"><?php echo $text_show_all ?></a>
    </span>
  </div>
  <?php } ?>
  <?php if (!$categories && !$products) { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
  </div>
  <?php } ?>
</div>
  
  </div>
  
   <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--
function display(view) {
	if (view == 'list') {
		$('.product-grid').attr('class', 'product-list');
		
		$('.product-list > div').each(function(index, element) {
			html  = '<div class="right">';
			html += '  <div class="cart">' + $(element).find('.cart').html() + '</div>';
			html += '  <div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			html += '  <div class="compare">' + $(element).find('.compare').html() + '</div>';
			html += '</div>';			
			
			html += '<div class="left">';
			
			var image = $(element).find('.image').html();
			
			if (image != null) { 
				html += '<div class="image">' + image + '</div>';
			}
			
			var price = $(element).find('.price').html();
			
			if (price != null) {
				html += '<div class="price">' + price  + '</div>';
			}
					
			html += '  <div class="name">' + $(element).find('.name').html() + '</div>';
			html += '  <div class="description">' + $(element).find('.description').html() + '</div>';
			
			var rating = $(element).find('.rating').html();
			
			if (rating != null) {
				html += '<div class="rating">' + rating + '</div>';
			}
				
			html += '</div>';

						
			$(element).html(html);
		});		
		
		$('.display').html('<b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display(\'grid\');"><?php echo $text_grid; ?></a>');
		
		$.cookie('display', 'list'); 
	} else {
		$('.product-list').attr('class', 'product-grid');
		
		$('.product-grid > div').each(function(index, element) {
			html = '';
			
			var image = $(element).find('.image').html();
			
			if (image != null) {
				html += '<div class="image">' + image + '</div>';
			}
			
			html += '<div class="name">' + $(element).find('.name').html() + '</div>';
			html += '<div class="description">' + $(element).find('.description').html() + '</div>';
			
			var price = $(element).find('.price').html();
			
			if (price != null) {
				html += '<div class="price">' + price  + '</div>';
			}
			
			var rating = $(element).find('.rating').html();
			
			if (rating != null) {
				html += '<div class="rating">' + rating + '</div>';
			}
						
			html += '<div class="cart">' + $(element).find('.cart').html() + '</div>';
			html += '<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			html += '<div class="compare">' + $(element).find('.compare').html() + '</div>';
			
			$(element).html(html);
		});	
					
		$('.display').html('<b><?php echo $text_display; ?></b> <a onclick="display(\'list\');"><?php echo $text_list; ?></a> <b>/</b> <?php echo $text_grid; ?>');
		
		$.cookie('display', 'grid');
	}
}

view = $.cookie('display');

if (view) {
	display(view);
} else {
	display('list');
}
//--></script> 
<?php echo $footer; ?>