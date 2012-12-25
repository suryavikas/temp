<?php
// cr2htmlmodule v1.5c
if ($borderless) {
     echo $code;
} else { ?>
<div class="box <?php echo $classname; ?>">
  <div class="box-heading" <?php if (!$title) {echo "style=\"height: 5px; background-image: none; border-bottom: none; padding: 0 !important;\""; }; ?>>
	<?php if($title) { echo $title; } else { echo "&nbsp;"; } ?>
  </div>
    <div class="box-content" style="text-align: left;">
    <?php echo $code; ?>
  </div>
</div>
<?php }; ?>