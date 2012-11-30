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
          <li><a href="index.php?route=account/guest">Guest Order</a></li>
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
      <div class="column">Newsletter
      <div>&nbsp;</div>
        <div id="newsletter">

 <script type="text/javascript">
    $(document).ready(function(){
	//$("#subscribe").validate();
	$("#subscribenewsletter1").click(function(){
	$("input#emailconfirm1").val($("input#email1").val());
	var str = $("form").serialize();
	 $.ajax({
          type:'post',
          url: 'http://localhost/lists/?p=subscribe&id=3',
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
          <form method="post" name="subscribeform" id="subscribe">
            <input type="hidden" name="formtoken" value="25b143b041e45e30cc3dddbb02fb96b9" />
            <table>
              <tr>
                <td class="attributeinput"><input type="text"  name="email" id="email1" value="" placeholder="Email" size="35" />
                </td>
              </tr>
              <tr>
               <input type="hidden" id="emailconfirm1" name="emailconfirm" value="">
              </tr>
              <input type="hidden" name="htmlemail" value="1">
            </table>
            <p>Please select the newsletters you want to sign up to:</p>
            <ul class="list">
              <li class="list">
              <input type="checkbox" name="list[2]" value=signup  />
              <input type=hidden name="listname[2]" value="Men"/>
              <b>Men</b>

           &nbsp;&nbsp;&nbsp;
              <input type="checkbox" name="list[3]" value=signup  />
               <input type=hidden name="listname[3]" value="Women"/>
              <b>Women</b>
            </li>
            </ul>
            <div style="display:none">
              <input type="text" name="VerificationCodeX" value="" size="20">
            </div>
            <p>
              <input type="hidden"  name="subscribe" id="subscribenewsletter" value="Subscribe"/>
              <input type="button" name="subscribe" id="subscribenewsletter1" value="Subscribe to the Selected Mailinglists"/>
            </p>
          </form>
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
</body></html>