<div id="banner">
  <div id="banner_img">
 
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
function mycarousel_initCallback(carousel){
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

$(document).ready(function() {
    $('#mycarousel').jcarousel({
        auto: 1	,
        wrap: 'last',
        initCallback: mycarousel_initCallback
    });

});
--></script>