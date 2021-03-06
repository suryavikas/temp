<div class="box">
  <div><h1 style="float:left; width:300px;"><?php echo $heading_title; ?></h1></div>
  <div class="box-content">
    <div class="box-product">
      <?php
        foreach ($products as $product) { ?>
      <div class ="img1">
          <?php if($product['special']){ ?>
            <div class="discount_tag"><?php echo $product['discount_percentage']; ?>%<br>OFF</div>
            <?php }
            ?>
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
        
        <div class="cart">
            <input type="button" value="<?php echo $button_cart; ?>" onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button" />
<!--            <input type="button" value="<?php //echo $button_buy_now; ?>" onclick="buyNow('<?php //echo $product['product_id']; ?>');" class="button" />-->
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
</div>
