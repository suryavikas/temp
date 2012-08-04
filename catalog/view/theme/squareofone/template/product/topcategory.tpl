<?php

echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>   
    <div class="mens_prd_detail">
        <div class="prds_img1">
            <div class="prds_img1_hd"><?php echo $heading_title; ?></div>
            <div class="clear"></div>
            <div class="prds_img1_txt">
                <a href="#"><img src="<?php echo $thumb; ?>" alt="" border="0" align="left" />Up to <br />
                    30%off</a>
            </div>
            <div class="clear"></div>
        </div>
        <?php
        $header = 0;
        for ($i=0 ; $i < count($categories); $i++) {
            if($i == 4){
                break;
            }
            echo '<ul>';
            ?>
            <li><strong><a href="<?php echo $categories[$i]['href']; ?>"><?php echo $categories[$i]['name']; ?></a></strong></li>
            <?php
            if (isset($categories[$i]['children'])) {
                $sizeOfChildren = count($categories[$i]['children']);
                for ($k = 0; $k <$sizeOfChildren; $k++) {
                    if($k == 5){
                        break;
                    }
                    if (count($categories[$i]['children'][$k]) > 0) { ?>
        <li>
            <a href="<?php echo $categories[$i]['children'][$k]['href']; ?>"><?php echo $categories[$i]['children'][$k]['name']; ?></a>
        </li>
                <?php
                    }
                }
            }
            echo'</ul>';
        } ?>
        <div class="clear"></div>
    </div>
    <!-- Total products in top category -->
    <div class="found_hd">
        We found <?php echo $product_total; ?> Products for All Men's
        <div class="clear"></div>
    </div>
    <!--Products inside category -->
    <?php 
        for($j = 0; $j<count($category_product_arr); $j++ ){
            if(isset($category_product_arr[$j]['category']['products']) && sizeof($category_product_arr[$j]['category']['products']) > 0){
    ?>
            <div class="found_prd_div">
                <h5><?php echo $category_product_arr[$j]['category']['name']; ?></h5>
                <div class="view_more_prd">
                    <div class="view_more_btn"><a href="<?php echo $category_product_arr[$j]['category']['href']; ?>"><img src="catalog/view/theme/squareofone/image/view-more-btn.jpg" alt="" border="0" /></a></div>
<!--                    <div class="view_page">
                        <div class="arw_left"><a href="#"><img src="catalog/view/theme/squareofone/image/arw-left.png" alt="" border="0" /></a></div>
                        <div class="arw_right"><a href="#"><img src="catalog/view/theme/squareofone/image/arw-right.png" alt="" border="0" /></a></div>
                        <div class="clear"></div>
                    </div>-->
                    <div class="clear"></div>
                </div>
                    <div class="clear"></div>
                <div class="bdr"><img src="catalog/view/theme/squareofone/image/bdr.jpg" alt="" border="0" /></div>
                <div class="clear"></div>
                <div class="found_prd_item">
                <ul>
    <?php
            if(isset($category_product_arr[$j]['category']['products'])){
                $productArr = $category_product_arr[$j]['category']['products'];
            } else{
               $productArr = array();
            }
                for($i = 0; $i < count($productArr); $i++){
    ?>                  
                    <li>
                        <img src="<?php echo $productArr[$i]['thumb']; ?>" alt="" border="0"  />
                        <div class="clear"></div>
                        <div class="buy_detail5">
                            <?php echo $productArr[$i]['name'] ?><br />
                            <div class="prc">
                                <div class="prc_left">Price : </div>   		
                                <div class="prc_right">
                                    <?php
                                        
                                        if(isset($productArr[$i]['discount']['price'])) {
                                    ?>
                                        <span><?php echo $productArr[$i]['price']; ?></span>
                                        <?php
                                        echo $productArr[$i]['discount']['price'];

                                    } else{
                                        echo $productArr[$i]['price'];
                                    }
                                    
                                     ?>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <?php
                            //TO Be decided on savings
                             if(isset($productArr[$i]['discount']['price'])) {
                                 
                            ?>

<!--                            <div class="prc">
                                <div class="prc_left">Savings :</div>
                                    <div class="prc_right"><?php echo  $productArr[$i]['savings'] ?></div>
                                <div class="clear"></div>
                            </div>-->
                            <?php
                             }
                            ?>
                            <div class="clear"></div>
                            <a href="<?php echo $productArr[$i]['href'] ?>" class="buy_now_button"><span>more</span></a>
                            <div class="clear"></div>
                        </div>
                    </li>
                
        
        <?php
                 }
        ?>
                    </ul>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
          </div>
    <?php
           }
        }
    ?>

    <?php if ($products) { ?>

    <div class="product-compare"><a href="<?php echo $compare; ?>" id="compare-total"><?php echo $text_compare; ?></a></div>
    <div class="product-list">
        <?php foreach ($products as $product) { ?>
        <div>
            <?php if ($product['thumb']) { ?>
            <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
            <?php } ?>
            <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
            <div class="description"><?php echo $product['description']; ?></div>
            <?php if ($product['price']) { ?>
            <div class="price">
                <?php if (!$product['special']) { ?>
                <?php echo $product['price']; ?>
                <?php } else { ?>
                <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
                <?php } ?>
                <?php if ($product['tax']) { ?>
                <br />
                <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                <?php } ?>
            </div>
            <?php } ?>
            <?php if ($product['rating']) { ?>
            <div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
            <?php } ?>
            <div class="cart">
                <input type="button" value="<?php echo $button_cart; ?>" onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button" />
            </div>
            <div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');"><?php echo $button_wishlist; ?></a></div>
            <div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');"><?php echo $button_compare; ?></a></div>
        </div>
        <?php } ?>
    </div>
    <div class="pagination"><?php echo $pagination; ?></div>
    <?php } ?>
    <?php if (!$categories && !$products) { ?>
    <div class="content"><?php echo $text_empty; ?></div>
    <div class="buttons">
        <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
    </div>
    <?php } ?>
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