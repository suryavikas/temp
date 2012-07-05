<?php if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>">
<head>
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ($icon) { ?>
<link href="<?php echo $icon; ?>" rel="icon" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<!-- IE Hacks -->
<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="en" version="HTML+RDFa 1.1"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="en" version="HTML+RDFa 1.1"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="en" version="HTML+RDFa 1.1"> <![endif]-->
<!--[if IE 9]>
<html class="no-js ie9" lang="en" version="HTML+RDFa 1.1"> <![endif]-->
<!--[if gt IE 9]><!-->
<html class=" js flexbox cssgradients csstransitions" version="HTML+RDFa 1.1" lang="en">
<!--<![endif]-->

<!-- CSS -->
<link rel="stylesheet" type="text/css" href="catalog/view/theme/squareofone/stylesheet/alice-min.css" media="all">
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/colorbox/colorbox.css" media="screen" />
<link rel="stylesheet" type="text/css" href="catalog/view/theme/squareofone/stylesheet/stylesheet.css" />

<link rel="stylesheet" type="text/css" href="catalog/view/theme/squareofone/stylesheet/skin.css" />
<link rel="stylesheet" type="text/css" href="catalog/view/theme/squareofone/stylesheet/style.css" />
<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

<!-- Java script -->
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/colorbox/jquery.colorbox.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/tabs.js"></script>
<script type="text/javascript" src="catalog/view/javascript/common.js"></script>
<!--<script type="text/javascript" src="catalog/view/javascript/jquery.jcarousel.min.js"></script>-->
<!--<script type="text/javascript" src="catalog/view/javascript/alice-1339158486.js"></script>-->
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/squareofone/stylesheet/ie7.css" />
<![endif]-->
<!--[if lt IE 7]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/squareofone/stylesheet/ie6.css" />
<script type="text/javascript" src="catalog/view/javascript/DD_belatedPNG_0.0.8a-min.js"></script>
<script type="text/javascript">
DD_belatedPNG.fix('#logo img');
</script>
<![endif]-->


<?php echo $google_analytics; ?>
</head>
<body>
    <div id="header">
    <div id="header_top1_outer">
    <div id="header_top1_inner">
      <div class="header_top1_menu">24x7 Customer Support  - <a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a> | <a href="<?php echo $home; ?>"><?php echo $text_home; ?></a> | <a href="#">Account</a> | <a href="<?php echo $wishlist; ?>"  id="wishlist-total"><?php echo $text_wishlist; ?></a> |

    <?php if (!$logged) { ?>
        <?php echo $text_welcome; ?>
    <?php } else { ?>
        <?php echo $text_logged; ?>
    <?php } ?>
        </div>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
    </div>
  <?php if ($logo) { ?>
     <div id="header_top2_outer">
        <div id="header_top2_inner">
            <div class="logo"> <a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" border="0" /></a></div>
                <div class="header_top2_right">
                    <div class="search_box">
                         <?php if ($filter_name) { ?>
                            <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" />
                            <?php } else { ?>
                            <input type="text" name="filter_name" value="<?php echo $text_search; ?>" onclick="this.value = '';" onkeydown="this.style.color = '#000000';" />
                            <?php } ?>
                            <input type="image" src="catalog/view/theme/squareofone/image/srch-btn.jpg" class="srch_btn" />
                    </div>
                    <div class="free_shiping">
                        <div class="bn">
                            <a href="index.html" class="free_shiping_hv"><span>Readmore</span></a>
                        </div>
                    </div>
        <div class="clear"></div>
      </div>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div>
  <?php }
  //commented till we figure out where to put
  ?>
  <?php // echo $language; ?>
  <?php // echo $currency; ?>
  <?php //echo $cart; ?>
</div>
    <div id="header_top3_outer">
    <div id="header_top3_inner">
      <div class="header_top3">
        <div class="home_icon"><a href="<?php echo $home; ?>"><img src="catalog/view/theme/squareofone/image/home-icon.png" alt="" border="0" /></a></div>
        <select name="" class="catg">
          <option>All Categories</option>
          <option>Sample</option>
        </select>
        <input type="text" value="Search for items" name="" class="srch" onFocus="if(this.value=='Search for items'){this.value=''}" onclick="if(this.value=='Search for items'){this.value=''}" onblur="if(this.value==''){this.value='Search for items'}" />
        <input type="image" src="catalog/view/theme/squareofone/image/go-btn.jpg" class="go_btn" />
        <div class="sale_btn"><a href="#"><img src="catalog/view/theme/squareofone/image/sale-btn.jpg" alt="" border="0" /></a></div>
        <div id="cart" class="shoping_bag">
          <div class="cart_img"><a href="<?php echo $shopping_cart; ?>"</a><img src="catalog/view/theme/squareofone/image/cart-img.png" alt="" border="0" /></a></div>
          <div class="cart_txt heading"> <strong>Shopping Bag</strong> now in your cart <a><?php echo $product_count; ?> items </a>
            <div class="clear"></div>
          </div>
          <div class="clear"></div>
        </div>
        <div class="clear"></div>
      </div>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div>



<div id="container">
<?php if ($categories) { ?>
    <div id="header_top4_outer">
        <div id="header_top4_inner">
<div id="menu" class="menu">
    <ul class="nav-sub">
    <?php foreach ($categories as $category) { ?>
    <li>
        <a href="<?php echo $category['href']; ?>" id="cat_141" class="sel-cat-shoes"> <span class="nav-subTxt"><?php echo $category['name']; ?></span> </a>

      <?php if ($category['children']) { ?>
      <div class="nav-layer fsm box-bgcolor ">
        <div class="pam line">
             <div class="unit size1of3" >
                  <div class="prl">
                    <div class="line mts">
                        <img src="catalog/view/theme/squareofone/image/img9.png" alt="" border="0" />
                        <h2>Womens Jeans</h2>
                    </div>
                </div>
            </div>
            
                <?php
                foreach ($category['children'] as $subcategory) {
                        $loopCount = 0;
                        ?>
                    
                <div class="unit size1of3" >
                    <div class="prl">
                    <a href="<?php echo $subcategory['href']; ?>"><h3><?php echo $subcategory['name']; ?></h3></a>
                    <?php if (count($subcategory['children']) > 0) { ?>
                        <div class="line mts">
                           <ul class="cnv unit">
                            <?php
                                $sizeOfChildren = count($subcategory['children']);
                                for ($i = 0; $i <$sizeOfChildren; $i++) {
                                    if (count($subcategory['children'][$i]) > 0) { ?>
                                        <li class="cnv-level-1">
                                            <a href="<?php echo $subcategory['children'][$i]['href']; ?>"><span class="cnv-name pls"><?php echo $subcategory['children'][$i]['name']; ?></span> </a>
                                        </li>
                            <?php
                                    }
                                }
                            ?>
                            </ul>
                        </div>
                <?php
                        }
                        ?>
                    </div>
    </div>
          <?php          }
                ?>
                </div>
            </div>
        
   
    <?php } ?>
     </li>  
<?php } ?>
    </ul>
</div>
            <div class="clear"></div>
</div>
<div class="clear"></div>
</div>

          <?php } ?>
<div id="notification"></div>