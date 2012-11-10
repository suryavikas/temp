<script type="text/javascript">
    $(document).ready(function(){
    $('#subscribeform').live('click', function(){
    var email = $("#email1").val();
    var emailconfirm = document.getElementById("emailconfirm1").value=email;
    if ($('#Men').is(':checked')){
            var Men = $("#Men").val();
           //var Men1 =document.getElementById("listname[6]").value=Men;
        }
     if ($('#Women').is(':checked')){
        var Women = $("#Women").val();
        //var women1=document.getElementById("listname[5]").value=Women;
     }
    var dataString = 'email='+ email + '&Men1=' + Men + '&Women1='+ Women + '&emailconfirm='+ emailconfirm;
    $.ajax({  
          type:'post',  
          url: 'http://localhost/lists/?p=subscribe&id=3',
          data: dataString,
          success: function(){
                  $("#message").html("News Subscribe Form Submitted!");
                  $('#newsletter').hide();
           }		
         });
     });
     return false;
 }); 
</script>

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
      <li><a href="http://localhost/squareofone/index.php?route=account/guest"><?php echo $text_guestorder; ?></a></li>
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
    <div id="newsletter">
      <form method=post  id="subscribeform1" name="subscribeform1">
      <input type="hidden" name="formtoken" value="280dc1c3946c44e757b1fcc19b55c90c"/>
  <table border=0>
    <tr>
      <td><div>Email</div></td>
      <td class="attributeinput"><input type="text" name="email" id="email1"   size="25" />
          <input type="hidden" name="emailconfirm" id="emailconfirm1"  size="25"/>
      </td>
    </tr>
  </table>
  <p>Please select the newsletters you want to sign up to:</p>
  <ul class="list">
    <li class="list">
      <input type="checkbox" name="Men" id="Men" value="Men" />
      <input type=hidden name="listname[6]" id="listname[6]" value="Men"/>
      <b>Men</b>
    </li>
    <li class="list">
      <input type="checkbox" name="Women" id="Women" value="Women" />
      <input type=hidden name="listname[5]" id="listname[5]" value="Women"/>
      <b>Women</b>
    </li>
  </ul>
  <p>
      <input type="button" value="Subscribe to the Selected Mailinglists" id="subscribeform" name="subscribe"/>
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