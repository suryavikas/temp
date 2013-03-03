<div class="box">
    
    <div>
        <h1 style="float:left; width:350px;"><?php if ($testimonial_title == "") echo "<br>"; else echo $testimonial_title; ?></h1>
        
    </div>
    <div style="float:right; margin-top: 20px;" class="name">
        <span style="vertical-align: middle;">
        <a href="<?php echo $showall_url; ?>"><?php echo $show_all; ?></a> | <a href="<?php echo $isitesti; ?>"><?php echo $isi_testimonial; ?></a>
        </span>
    </div>


    <div class="box-content testimonial">
        <div class="box-product testimonial">

            <ul id="button_quotes">
                <?php
                    $i = "block";
                    foreach ($testimonials as $testimonial) {
                ?>

                    <li style="display: <?php echo $i; ?>; opacity: 1;" class="">
                        <b><?php echo $testimonial['title']; ?></b>
                        <blockquote><?php echo $testimonial['description']; ?></blockquote>
                        
                        <?php if ($testimonial['rating']) { ?>
                            <img src="catalog/view/theme/default/image/stars-<?php echo $testimonial['rating'] . '.png'; ?>" style="margin-top: 2px;" />
                        <?php } ?>
                        <?php if ($testimonial['name'] != "") echo '<br>' . $testimonial['name']; else echo $testimonial['name']; ?>
                        <cite><?php echo $testimonial['city']; ?></cite>
                    </li>

                <?php $i = "none"; } ?>
            </ul>
        </div>
        
    </div>
</div>
<script type="text/javascript">
    $('ul#button_quotes').quote_rotator();
</script>