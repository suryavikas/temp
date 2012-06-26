<div id="banner">
  <div id="banner_img">
  <div class="banner_menu">
  <div class="banner_menu_inner">
    <ul>
       <li><strong>Men</strong></li>
<li><a href="#">Casual Shoes</a></li>
<li><a href="#">Formal Shoes</a></li>
<li><a href="#">Sports Shoes</a></li>
<li><a href="#">Sandals</a></li>
<li><a href="#">Shirts</a></li>
<li><a href="#">Jeans</a></li>
<li><a href="#">Watches & Accessories</a></li>
<div class="clear"></div>
    </ul>

    <ul>
    <li><strong>Women</strong></li>
<li><a href="#">Slippers</a></li>
<li><a href="#">Casual Shoes</a></li>
<li><a href="#">Sandals</a></li>
<li><a href="#">Kurtas & Kurtis</a></li>
<li><a href="#">Sarees</a></li>
<li><a href="#">Tops</a></li>
<li><a href="#">Bags & Accessories</a></li>
    <div class="clear"></div>
    </ul>


    <ul>
      <li><a href="#"><strong>Kids</strong></a></li>
<li><a href="#"><strong>Beauty</strong></a></li>
<li><a href="#"><strong>Sports</strong></a></li>
<li><a href="#"><strong>Home & Living</strong></a></li>
      <div class="clear"></div>
    </ul>
  <div class="clear"></div>
  </div>
  <div class="clear"></div>
  </div>
  <div style=" overflow:hidden; position:relative; height:395px; display:block; margin:0; padding:0;">
<div class="cnt">
  <div id="slideshow<?php echo $module; ?>" class="cnt2" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px;">
    <ul id="mycarousel" class="jcarousel-skin-tango">
  <?php foreach ($banners as $banner) { ?>
    <?php if ($banner['link']) { ?>
        <li><div><a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" /></a>
    <?php } else { ?>
    <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" />
    <?php } ?>
        </div></li>
    <?php } ?>
    </ul>
  </div>
</div>
 </div>
  </div>
  </div>
  <div class="clear"></div>


<script type="text/javascript" src="catalog/view/javascript/jquery.jcarousel.min.js"></script>
<script type="text/javascript"><!--
function mycarousel_initCallback(carousel)
{
    // Disable autoscrolling if the user clicks the prev or next button.
    carousel.buttonNext.bind('click', function() {
        carousel.startAuto(0);
    });

    carousel.buttonPrev.bind('click', function() {
        carousel.startAuto(0);
    });

    // Pause autoscrolling if the user moves with the cursor over the clip.
    carousel.clip.hover(function() {
        carousel.stopAuto();
    }, function() {
        carousel.startAuto();
    });
};

//$(document).ready(function() {
    $('#mycarousel').jcarousel({
        auto: 3	,
        wrap: 'last',
        initCallback: mycarousel_initCallback
    });
    
//});
--></script>