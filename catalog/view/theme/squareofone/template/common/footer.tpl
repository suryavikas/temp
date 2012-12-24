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
          <li><a href="<?php echo $tellafriend; ?>"><?php echo $text_tellafriend; ?></a></li>
        </ul>
        <div class="clear"></div>
      </div>
      <div class="column">
        <h3><?php echo $text_service; ?></h3>
        <ul>
          <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
          <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
          <li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
          <li><a href="<?php echo $track_order; ?>"><?php echo $text_track_order; ?></a></li>
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
      <div class="column">Newsletter
      <div>&nbsp;</div>
        <div id="newsletter">

 <script type="text/javascript">
    $(document).ready(function(){
	//$("#subscribe").validate();
	$("#subscribenewsletter1").click(function(){
            errorStr = ''
            if($("#email1").val() == ''){
                errorStr = '<p>Pls enter an email address<p><br />';
            } else {
                var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                if( !emailReg.test( $email ) ) {
                    errorStr = 'Email address seems invalid'
                }
            }
//            $('#newletter-subscribe input[name=\'listname[2]\']')
            console.log($("#newletter-subscribe select[name='\'listname[2]\''] option:selected"));
//            if($('#newletter-subscribe input[name=\'listname[2]\']').c)
            
//        $("input#emailconfirm1").val($("input#email1").val());
	var str = $("form").serialize();
	 $.ajax({
          type:'post',
          url: './lists/?p=subscribe&id=3',
          data: str,
          success: function(){
                  $("#message").html("News Letter Subscribe Form Submitted!");
                  $('#newsletter').hide();
           }
         });
	});
 return false;
});
</script>

<script language="Javascript" type="text/javascript">

function checkform() {
  for (i=0;i<fieldstocheck.length;i++) {
    if (eval("document.subscribeform.elements['"+fieldstocheck[i]+"'].type") == "checkbox") {
      if (document.subscribeform.elements[fieldstocheck[i]].checked) {
      } else {
        alert("Please enter your "+fieldnames[i]);
        eval("document.subscribeform.elements['"+fieldstocheck[i]+"'].focus()");
        return false;
      }
    }
    else {
      if (eval("document.subscribeform.elements['"+fieldstocheck[i]+"'].value") == "") {
        alert("Please enter your "+fieldnames[i]);
        eval("document.subscribeform.elements['"+fieldstocheck[i]+"'].focus()");
        return false;
      }
    }
  }
  for (i=0;i<groupstocheck.length;i++) {
    if (!checkGroup(groupstocheck[i],groupnames[i])) {
      return false;
    }
  }

  if(! compareEmail())
  {
    alert("Email Addresses you entered do not match");
    return false;
  }
  return true;
}

var fieldstocheck = new Array();
var fieldnames = new Array();
function addFieldToCheck(value,name) {
  fieldstocheck[fieldstocheck.length] = value;
  fieldnames[fieldnames.length] = name;
}
var groupstocheck = new Array();
var groupnames = new Array();
function addGroupToCheck(value,name) {
  groupstocheck[groupstocheck.length] = value;
  groupnames[groupnames.length] = name;
}

function compareEmail()
{
  return (document.subscribeform.elements["email"].value == document.subscribeform.elements["emailconfirm"].value);
}
function checkGroup(name,value) {
  option = -1;
  for (i=0;i<document.subscribeform.elements[name].length;i++) {
    if (document.subscribeform.elements[name][i].checked) {
      option = i;
    }
  }
  if (option == -1) {
    alert ("Please enter your "+value);
    return false;
  }
  return true;
}
</script>
<div id="newletter-subscribe" style="overflow: hidden; min-height: 260px;  height: auto; width:300px">
          <iframe  src="http://www.newsletter.squareofone.com/?p=subscribe&id=1" style="border: 0px solid #ffffff; overflow: hidden; min-height: 260px;" scrolling="no">
            <p>Your browser does not support iframes.</p>
        </iframe>
        </div>
        </div>
        <div id="message" style="color:#F52887;"></div>
      </div>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div>
  <div id="footer_bottom">
    <div id="footer_bottom_inner">
      <div class="paypal_left">
        <ul>
          <li class="sq_paypal">&nbsp;</li>
          <li class="sq_mst1">&nbsp;</li>
          <li class="sq_mst2">&nbsp;</li>
          <li class="sq_visa1">&nbsp;</li>
          <li class="sq_visa2">&nbsp;</li>
          <li style="margin:0 0 10px 20px;" class="sq_nrt">&nbsp;</li>
        </ul>
        <div class="clear"></div>
      </div>
      <div class="follow">
        <ul>
          <li style="padding:0px; margin-right:-10px;" class="sq_flw">&nbsp;</li>
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
<script type="text/javascript">
//	window.addEventListener('load', function(){
//
//		helium.init();
//
//	}, false);
</script>
</body></html>