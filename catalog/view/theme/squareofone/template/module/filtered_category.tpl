
<?php if ($products) { ?>
    <!-- Product Filters -->    
  <div class="found_prd_item product-list">
      <ul>
    <?php foreach ($products as $product) {  ?>
    <li>
      <?php if($product['special']){ ?>
            <div class="discount_tag"><?php echo $product['discount_percentage']; ?>%<br>OFF</div>
      <?php }
      ?>
      <?php if ($product['thumb']) { ?>
            <?php if ($product['thumb_soldout'] != null) { ?><img style="position: absolute; pointer-events: none;" src="<?php echo $product['thumb_soldout']; ?>" title="" alt="" /><?php } ?>
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
             <a <?php if ($product['quantity'] <= 0) { echo 'style="display:none"'; } ?> class="buy_now_button" onclick="addToCart('<?php echo $product['product_id']; ?>');"><span>more</span></a>
            <div class="clear"></div>
    </div>

    </li>
    <?php } ?>
        </ul>
  </div>
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } ?>
  <?php if (!$categories && !$products) { ?>
  
  <div class="product-list"><?php echo $text_empty; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
  </div>
  <?php } ?>

