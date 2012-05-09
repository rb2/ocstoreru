<div class="left">
  <h2><?php echo $text_your_details; ?></h2>
  <span class="required">*</span> <?php echo $entry_firstname; ?><br />
  <input type="text" name="firstname" value="" class="large-field" />
  <br />
  <br />
  <span class="required">*</span> <?php echo $entry_lastname; ?><br />
  <input type="text" name="lastname" value="" class="large-field" />
  <br />
  <br />
  <span class="required">*</span> <?php echo $entry_email; ?><br />
  <input type="text" name="email" value="" class="large-field" />
  <br />
  <br />
  <span class="required">*</span> <?php echo $entry_telephone; ?><br />
  <input type="text" name="telephone" value="" class="large-field" />
  <br />
  <br />
  <?php echo $entry_fax; ?><br />
  <input type="text" name="fax" value="" class="large-field" />
  <br />
  <br />
  <h2><?php echo $text_your_password; ?></h2>
  <span class="required">*</span> <?php echo $entry_password; ?><br />
  <input type="password" name="password" value="" class="large-field" />
  <br />
  <br />
  <span class="required">*</span> <?php echo $entry_confirm; ?> <br />
  <input type="password" name="confirm" value="" class="large-field" />
  <br />
  <br />
  <br />
</div>
<div class="right">
  <h2><?php echo $text_your_address; ?></h2>
  <?php echo $entry_company; ?><br />
  <input type="text" name="company" value="" class="large-field" />
  <br />
  <br />
  <span class="required">*</span> <?php echo $entry_address_1; ?><br />
  <input type="text" name="address_1" value="" class="large-field" />
  <br />
  <br />
  <?php echo $entry_address_2; ?><br />
  <input type="text" name="address_2" value="" class="large-field" />
  <br />
  <br />
  <span class="required">*</span> <?php echo $entry_city; ?><br />
  <input type="text" name="city" value="" class="large-field" />
  <br />
  <br />
  <span id="payment-postcode-required" class="required">*</span> <?php echo $entry_postcode; ?><br />
  <input type="text" name="postcode" value="<?php echo $postcode; ?>" class="large-field" />
  <br />
  <br />
  <span class="required">*</span> <?php echo $entry_country; ?><br />
  <select name="country_id" class="large-field">
    <option value=""><?php echo $text_select; ?></option>
    <?php foreach ($countries as $country) { ?>
    <?php if ($country['country_id'] == $country_id) { ?>
    <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
    <?php } ?>
    <?php } ?>
  </select>
  <br />
  <br />
  <span class="required">*</span> <?php echo $entry_zone; ?><br />
  <select name="zone_id" class="large-field">
  </select>
  <br />
  <br />
  <br />
</div>
<div style="clear: both; padding-top: 15px; border-top: 1px solid #EEEEEE;">
  <input type="checkbox" name="newsletter" value="1" id="newsletter" />
  <label for="newsletter"><?php echo $entry_newsletter; ?></label>
  <br />
  <?php if ($shipping_required) { ?>
  <input type="checkbox" name="shipping_address" value="1" id="shipping" checked="checked" />
  <label for="shipping"><?php echo $entry_shipping; ?></label>
  <br />
  <?php } ?>
  <br />
  <br />
</div>
<?php if ($text_agree) { ?>
<div class="buttons">
  <div class="right"><?php echo $text_agree; ?>
    <input type="checkbox" name="agree" value="1" />
    <input type="button" value="<?php echo $button_continue; ?>" id="button-register" class="button" />
  </div>
</div>
<?php } else { ?>
<div class="buttons">
  <div class="right"><input type="button" value="<?php echo $button_continue; ?>" id="button-register" class="button" /></div>
</div>
<?php } ?>
<?php 
$postcode_required_data = array(); 

foreach ($countries as $country) {
	if ($country['postcode_required']) {
		$postcode_required_data[] = '\'' . $country['country_id'] . '\'';
	} 
} 
?>
<script type="text/javascript"><!--
$('#payment-address select[name=\'country_id\']').bind('change', function() {
	var postcode_required = [<?php echo implode(',', $postcode_required_data); ?>];
	
	if ($.inArray(this.value, postcode_required) >= 0) {
		$('#payment-postcode-required').show();
	} else {
		$('#payment-postcode-required').hide();
	}
	
	$('#payment-address select[name=\'zone_id\']').load('index.php?route=checkout/register/zone&country_id=' + this.value + '&zone_id=<?php echo $zone_id; ?>');	
});

$('#payment-address select[name=\'country_id\']').trigger('change');
//--></script> 
<script type="text/javascript"><!--
$('.colorbox').colorbox({
	width: 640,
	height: 480
});
//--></script> 