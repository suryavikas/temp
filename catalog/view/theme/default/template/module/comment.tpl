<?php
// ------------------------------------------------------
// TweetBook for Opencart
// By MarketInSG
// contact@marketinsg.com
// ------------------------------------------------------
?>

<!-- 
This module, Tweetbook, is free & is by MarketInSG (http://www.marketinsg.com)
If you appreciate our work, Visit http://www.marketinsg.com/donate to donate.
//-->

<?php
	if($face AND $twit) {
?>
		<div style="position:absolute; bottom:50px; <?php if($display){ echo "left:0px"; } else { echo "right:110px"; } ?>;">
<?php
}
else {
?>
		<div style="position:absolute; bottom:50px; <?php if($display){ echo "left:0px"; } else { echo "right:50px"; } ?>;">
<?php
}
?>
			<div style="position:fixed;">
				<?php if($face) { ?>
					<a href="http://www.facebook.com/<?php echo $facebook_url; ?>" title="Like <?php echo $store; ?> on Facebook" target="_blank"><img border="0" style="width:50px; height:50px;" src="catalog/view/theme/default/image/f_logo.png" alt="" /></a>
				<?php } ?>
				<?php if($twit) { ?>
					<a href="http://www.twitter.com/<?php echo $twitter_url; ?>" title="Follow <?php echo $store; ?> on Twitter" target="_blank"><img border="0" style="width:50px; height:50px;" src="catalog/view/theme/default/image/t_logo.png" alt="" /></a>
				<?php } ?>
			</div>
		</div>
