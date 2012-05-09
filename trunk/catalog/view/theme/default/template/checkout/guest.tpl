<div class="left">
  <h2><?php echo $text_your_details; ?></h2>
  <span class="required">*</span> <?php echo $entry_firstname; ?><br />
  <input type="text" name="firstname" value="<?php echo $firstname; ?>" class="large-field" />
  <br />
  <br />
  <span class="required">*</span> <?php echo $entry_lastname; ?><br />
  <input type="text" name="lastname" value="<?php echo $lastname; ?>" class="large-field" />
  <br />
  <br />
  <span class="required">*</span> <?php echo $entry_email; ?><br />
  <input type="text" name="email" value="<?php echo $email; ?>" class="large-field" />
  <br />
  <br />
  <span class="required">*</span> <?php echo $entry_telephone; ?><br />
  <input type="text" name="telephone" value="<?php echo $telephone; ?>" class="large-field" />
  <br />
  <br />
  <?php echo $entry_fax; ?><br />
  <input type="text" name="fax" value="<?php echo $fax; ?>" class="large-field" />
  <br />
  <br />
  <?php if ($customer_groups) { ?>
  <h2><?php echo $text_your_account; ?></h2>
  <?php echo $entry_account; ?><br />
  <select name="customer_group_id" class="large-field">
    <?php foreach ($customer_groups as $customer_group) { ?>
    <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
    <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
    <?php } ?>
    <?php } ?>
  </select>
  <br />
  <br />
  <?php } ?>  
</div>
<div class="right">
  <h2><?php echo $text_your_address; ?></h2>
  <?php echo $entry_company; ?><br />
  <input type="text" name="company" value="<?php echo $company; ?>" class="large-field" />
  <br />
  <br />
  <div id="company-id-display"><span id="company-id-required" class="required">*</span> <?php echo $entry_company_id; ?><br />
    <input type="text" name="company_id" value="" class="large-field" />
    <br />
    <br />
  </div>
  <div id="tax-id-display"><span id="tax-id-required" class="required">*</span> <?php echo $entry_tax_id; ?><br />
    <input type="text" name="tax_id" value="" class="large-field" />
    <br />
    <br />
  </div>  
  <span class="required">*</span> <?php echo $entry_address_1; ?><br />
  <input type="text" name="address_1" value="<?php echo $address_1; ?>" class="large-field" />
  <br />
  <br />
  <?php echo $entry_address_2; ?><br />
  <input type="text" name="address_2" value="<?php echo $address_2; ?>" class="large-field" />
  <br />
  <br />
  <span class="required">*</span> <?php echo $entry_city; ?><br />
  <input type="text" name="city" value="<?php echo $city; ?>" class="large-field" />
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
<?php if ($shipping_required) { ?>
<div style="clear: both; padding-top: 15px; border-top: 1px solid #DDDDDD;">
  <?php if ($shipping_address) { ?>
  <input type="checkbox" name="shipping_address" value="1" id="shipping" checked="checked" />
  <?php } else { ?>
  <input type="checkbox" name="shipping_address" value="1" id="shipping" />
  <?php } ?>
  <label for="shipping"><?php echo $entry_shipping; ?></label>
  <br />
  <br />
  <br />
</div>
<?php } ?>
<div class="buttons">
  <div class="right"><input type="button" value="<?php echo $button_continue; ?>" id="button-guest" class="button" /></div>
</div>
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
	
	$('#payment-address select[name=\'country_id\']').load('index.php?route=checkout/guest/zone&country_id=' + this.value + '&zone_id=<?php echo $zone_id; ?>');
});

$('#payment-address select[name=\'country_id\']').trigger('change');
//--></script>
<?php 
$company_id_display_data = array();
$company_id_required_data = array();
$tax_id_display_data = array();
$tax_id_required_data = array();

foreach ($customer_groups as $customer_group) {
	if ($customer_group['company_id_display']) {
		$company_id_display_data[] = '\'' . $customer_group['customer_group_id'] . '\'';
	}

    if ($customer_group['company_id_required']) {
    	$company_id_required_data[] = '\'' . $customer_group['customer_group_id'] . '\'';
    }


	if ($customer_group['tax_id_display']) {
		$tax_id_display_data[] = '\'' . $customer_group['customer_group_id'] . '\'';
	}

	if ($customer_group['tax_id_required']) {
		$tax_id_required_data[] = '\'' . $customer_group['customer_group_id'] . '\'';
	}
} 
?>
<script type="text/javascript"><!--
$('select[name=\'customer_group_id\']').live('change', function() {
	var company_id_display = [<?php echo implode(',', $company_id_display_data); ?>];
	
	if ($.inArray(this.value, company_id_display) >= 0) {
		$('#company-id-display').show();
	} else {
		$('#company-id-display').hide();
	}
	
	var company_id_required = [<?php echo implode(',', $company_id_required_data); ?>];
	
	if ($.inArray(this.value, company_id_required) >= 0) {
		$('#company-id-required').show();
	} else {
		$('#company-id-required').hide();
	}
	
	var tax_id_display = [<?php echo implode(',', $tax_id_display_data); ?>];
	
	if ($.inArray(this.value, tax_id_display) >= 0) {
		$('#tax-id-display').show();
	} else {
		$('#tax-id-display').hide();
	}
	
	var tax_id_required = [<?php echo implode(',', $tax_id_required_data); ?>];
	
	if ($.inArray(this.value, tax_id_required) >= 0) {
		$('#tax-id-required').show();
	} else {
		$('#tax-id-required').hide();
	}
});

$('select[name=\'customer_group_id\']').trigger('change');
//--></script>