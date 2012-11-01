<?php
// ------------------------------------------------------
// TweetBook for Opencart
// By MarketInSG
// contact@marketinsg.com
// ------------------------------------------------------
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#user-feedback input[type=\"radio\"]').live('click', function() {
			if($(this).attr('id') == 'happy'){				
				 $("#facebook").show();
				 $("#contact-us").hide();
			} else{
				$("#facebook").hide();
				 $("#contact-us").show();
			}
	});
	$('#comment-submit').live('click', function(){	
	
	 var flag = 1
	 if(document.forms["contact-us"]["name"].value=="")
       {
		   alert("Please Fill Your Name");
		   document.forms["contact-us"]["name"].focus();
		  flag = 0
       }
	   else if(document.forms["contact-us"]["email"].value=="")
       {
		   alert("Please Fill Your Email");
		   document.forms["contact-us"]["email"].focus();
		  flag = 0
       }
	   else if(document.forms["contact-us"]["enquiry"].value=="")
       {
		   alert("Please Fill Your Enquiry");
		   document.forms["contact-us"]["enquiry"].focus();
		  flag = 0
       }
		if(flag==1)
	  {  
		$.ajax({
			url: 'index.php?route=module/comment/comment_submit',
			type: 'post',
			data: 	$('#contact-us input[type=\'text\'], #contact-us textarea'),
			dataType: 'json',
			success: function(json) {
				if(json['success']){
					$('#contact-us').html(json['success']);
				} else{
					$('#contact-us h2').after('<p>'+json['error']+'</p>');
				}
			}
		});
		
		}
	});
	 
 });
</script>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
			
			<div id="user-feedback" style="width:342px; color:#3333FF; font-size:15px; position:absolute;
            bottom:0; right:0; font-weight:bold;"><div class="fb-like" data-href="http://localhost/squareofone/" data-send="true" data-width="450" data-show-faces="true"></div>About Us
				
                	<input type="radio" id="happy"  name="myshop" style="margin:0 8px 0 0;"/>Happy with Us
            		<input type="radio" id="unhappy" name="myshop" style="margin:0 8px 0 0;"/>UnHappy with Us
                 </div>
                <div id ="facebook" style="display:none">
                <div class="fb-comments" data-href="http://localhost/squareofone/" data-num-posts="0" data-width="470">                </div>
           	   </div>
  
            	<div id="contact-us" style="display:none">
                    <form name="contact-us" id="contact-us" method="POST" >
                    <div class="content" style="float:right; width:324px; padding-bottom:15px;">
                    <h2><?php echo $text_contact; ?></h2>
                    <div class="ct_lt" style="padding:10px 10px 0 10px; background:#EAEAEA;">
                    <b><?php echo $entry_name; ?></b><br />
                    <input type="text" name="name" id="name"  class="inpt" />
                    <br />
                    <?php if ($error_name) { ?>
                    <span class="error"><?php echo $error_name; ?></span>
                    <?php } ?>
                    <br />
                    <b><?php echo $entry_email; ?></b><br />
                    <input type="text" name="email" id="email" class="inpt" />
                    <br />
                    <?php if ($error_email) { ?>
                    <span class="error"><?php echo $error_email; ?></span>
                    <?php } ?>
                    <br />
                    <b><?php echo $entry_enquiry; ?></b><br />
                    <textarea name="enquiry" id="enquiry" class="msg"></textarea>
                    <br />
                    <br />
                    <div class="clear"></div>
                    <div class="button">
                    <input type="button" id="comment-submit" value="submit" class="button"/>
                    </div>
                    <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                    </div>
                    <br class="clear" /> 
                    </form>              
		   		 </div>
                 


