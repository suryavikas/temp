<!--<div id="footer">
  <div class="column">
    <h3><?php echo $text_information; ?></h3>
    <ul>
      <?php foreach ($informations as $information) { ?>
      <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
      <?php } ?>
    </ul>
  </div>
  <div class="column">
    <h3><?php echo $text_service; ?></h3>
    <ul>
      <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
      <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
      <li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
    </ul>
  </div>
  <div class="column">
    <h3><?php echo $text_extra; ?></h3>
    <ul>
      <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
      <li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
      <li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
      <li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
    </ul>
  </div>
  <div class="column">
    <h3><?php echo $text_account; ?></h3>
    <ul>
      <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
      <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
      <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
      <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
    </ul>
  </div>
</div>-->
<div id="footer">
<div id="footer_top">
<div id="footer_top_inner">
    <!-- Information -->
    <div class="column">
    <h3><?php echo $text_information; ?></h3>
    <ul>
      <?php foreach ($informations as $information) { ?>
      <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
      <?php } ?>
    </ul>
    <div class="clear"></div>
    </div>

<div class="column">
    <h3><?php echo $text_service; ?></h3>
    <ul>
      <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
      <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
      <li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
    </ul>
  </div>

<div class="column">
    <h3><?php echo $text_account; ?></h3>
    <ul>
      <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
      <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
      <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
      <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
    </ul>
  </div>

     <div class="column">
    <h3><?php echo $best_seller; ?></h3>
    <ul>
      <?php foreach ($best_sellers as $best_seller) { ?>
      <li><a href="<?php echo $best_seller['href']; ?>"><?php echo $best_seller['name']; ?></a></li>
      <?php } ?>
    </ul>
  </div>
<div class="column">

Sign up to receive special offers and the latest style news.
<div class="clear"></div>
<input type="text" value="Enter Your Email Address" name="" class="mals" onFocus="if(this.value=='Enter Your Email Address'){this.value=''}" onclick="if(this.value=='Enter Your Email Address'){this.value=''}" onblur="if(this.value==''){this.value='Enter Your Email Address'}" />
<div class="clear"></div>
<input type="image" src="catalog/view/theme/squareofone/image/sign-up.jpg" class="sin_up" />
<div class="clear"></div>
</div>
<div class="clear"></div>
</div>
<div class="clear"></div>
</div>
<div id="footer_bottom">
<div id="footer_bottom_inner">
 <div class="paypal_left">
   <ul>
     <li><img src="catalog/view/theme/squareofone/image/paypal.jpg" alt="" border="0" /></li>
     <li><img src="catalog/view/theme/squareofone/image/ms1.jpg" alt="" border="0" /></li>
     <li><img src="catalog/view/theme/squareofone/image/ms2.jpg" alt="" border="0" /></li>
     <li><img src="catalog/view/theme/squareofone/image/visa1.jpg" alt="" border="0" /></li>
     <li><img src="catalog/view/theme/squareofone/image/visa2.jpg" alt="" border="0" /></li>
     <li style="padding-top:0px; padding-left:20px;"><img src="catalog/view/theme/squareofone/image/norton.jpg" alt="" border="0" /></li>
   </ul>
   <div class="clear"></div>
 </div>
 <div class="follow">
  <ul>
    <li style="padding:0px; margin-right:-10px;"><img src="catalog/view/theme/squareofone/image/follow.jpg" alt="" border="0" /></li>
    <li><a href="#" class="tw_hv"><span>more</span></a></li>
    <li><a href="#" class="rss_hv"><span>more</span></a></li>
    <li><a href="#" class="fb_hv"><span>more</span></a></li>
    <li><a href="#" class="v_hv"><span>more</span></a></li>

  </ul>
 <div class="clear"></div>
 </div>
<div class="clear"></div>
</div>
<div class="clear"></div>
</div>
<div class="clear"></div>
</div>

</div>
</body></html>