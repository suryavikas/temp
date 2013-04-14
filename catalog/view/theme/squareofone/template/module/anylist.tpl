<div class="box">
    <?php echo ($titlelink) ? '<a href="'.$titlelink.'">' : ''; ?>
  <div><h1 style="float:left; width:auto;"><?php echo $heading_title; ?></h1></div>
  <?php echo ($titlelink) ? '</a>' : ''; ?>
	<?php if (isset($grid) and $grid>0) { ?>
	    <ul class="product-grid">
	      <?php foreach ($products as $product) { ?>
			<li>
				<div class="right">
					<div class="cart"><input value="Add to Cart" onclick="addToCart('41');" class="button" type="button"/></div>
					<div class="wishlist"><a onclick="addToWishList('41');">Add to Wish List</a></div>
					<div class="compare"><a onclick="addToCompare('41');">Add to Compare</a></div>
				</div>
				<div class="left">
				<?php if ($product['thumb']) { ?>
		        <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
		        <?php } ?>
	        
					<div class="price">
			          <?php if (!$product['special']) { ?>
			          <?php echo $product['price']; ?>
			          <?php } else { ?>
			          <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
			          <?php } ?>
						<?php if (isset($product['tax']) and $product['tax']) { ?>
						<br />
						<span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
						<?php } ?>
					</div>
	        <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
					<div class="description">
				<?php echo mb_substr(html_entity_decode($product['description'],ENT_QUOTES,'UTF-8'),0,200); ?>
					</div>
				</div>
			</li>    
	      <?php } ?>
	    </ul>
   <?php } else { ?>
		<div class="box-content">
    <div class="box-product">
      <?php
        foreach ($products as $product) { ?>
      <div class ="img1">           
        <?php if ($product['thumb']) { ?>
        <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
        <?php } ?>
        <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
        <?php if ($product['price']) { ?>
        <div class="price">
          <?php if (!$product['special']) { ?>
          <?php echo $product['price']; ?>
          <?php } else { ?>
          <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
          <?php } ?>
        </div>
        <?php } ?>
        <?php if ($product['rating']) { ?>
<!--        <div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>-->
        <?php } ?>
        <div class="cart">
            <input type="button" value="<?php echo $button_cart; ?>" onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button" />
<!--            <input type="button" value="<?php //echo $button_buy_now; ?>" onclick="buyNow('<?php //echo $product['product_id']; ?>');" class="button" />-->

        </div>
      </div>
      <?php } ?>
    </div>
  </div>
   <?php } ?>
    
</div>
