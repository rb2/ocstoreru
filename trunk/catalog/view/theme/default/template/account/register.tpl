<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  <p><?php echo $text_account_already; ?></p>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
    <h2><?php echo $text_your_details; ?></h2>
    <div class="content">
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $entry_firstname; ?></td>
          <td><input type="text" name="firstname" value="<?php echo $firstname; ?>" />
            <?php if ($error_firstname) { ?>
            <span class="error"><?php echo $error_firstname; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
          <td><input type="text" name="lastname" value="<?php echo $lastname; ?>" />
            <?php if ($error_lastname) { ?>
            <span class="error"><?php echo $error_lastname; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_email; ?></td>
          <td><input type="text" name="email" value="<?php echo $email; ?>" />
            <?php if ($error_email) { ?>
            <span class="error"><?php echo $error_email; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_telephone; ?></td>
          <td><input type="text" name="telephone" value="<?php echo $telephone; ?>" />
            <?php if ($error_telephone) { ?>
            <span class="error"><?php echo $error_telephone; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><?php echo $entry_fax; ?></td>
          <td><input type="text" name="fax" value="<?php echo $fax; ?>" /></td>
        </tr>
      </table>
    </div>
    <h2><?php echo $text_your_address; ?></h2>
    <div class="content">
      <table class="form">
        <tr>
          <td>Account Type:</td>
          <td>Indvidual (<a id="button-account">Change</a>)</td>
        </tr>      
        <tr>
          <td><?php echo $entry_company; ?></td>
          <td><input type="text" name="company" value="<?php echo $company; ?>" /></td>
        </tr>
        <tr id="company-id-display" style="display: none;">
          <td><span id="company-id-required" class="required">*</span> <?php echo $entry_company_id; ?></td>
          <td><input type="text" name="company_id" value="<?php echo $company_id; ?>" />
            <?php if ($error_company_id) { ?>
            <span class="error"><?php echo $error_company_id; ?></span>
            <?php } ?></td>
        </tr>
        <tr id="tax-id-display" style="display: none;">
          <td><span id="tax-id-required" class="required">*</span> <?php echo $entry_tax_id; ?></td>
          <td><input type="text" name="tax_id" value="<?php echo $tax_id; ?>" />
            <?php if ($error_tax_id) { ?>
            <span class="error"><?php echo $error_tax_id; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_address_1; ?></td>
          <td><input type="text" name="address_1" value="<?php echo $address_1; ?>" />
            <?php if ($error_address_1) { ?>
            <span class="error"><?php echo $error_address_1; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><?php echo $entry_address_2; ?></td>
          <td><input type="text" name="address_2" value="<?php echo $address_2; ?>" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_city; ?></td>
          <td><input type="text" name="city" value="<?php echo $city; ?>" />
            <?php if ($error_city) { ?>
            <span class="error"><?php echo $error_city; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span id="postcode-required" class="required">*</span> <?php echo $entry_postcode; ?></td>
          <td><input type="text" name="postcode" value="<?php echo $postcode; ?>" />
            <?php if ($error_postcode) { ?>
            <span class="error"><?php echo $error_postcode; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_country; ?></td>
          <td><select name="country_id">
              <option value=""><?php echo $text_select; ?></option>
              <?php foreach ($countries as $country) { ?>
              <?php if ($country['country_id'] == $country_id) { ?>
              <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
            <?php if ($error_country) { ?>
            <span class="error"><?php echo $error_country; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_zone; ?></td>
          <td><select name="zone_id">
            </select>
            <?php if ($error_zone) { ?>
            <span class="error"><?php echo $error_zone; ?></span>
            <?php } ?></td>
        </tr>
      </table>
    </div>
    <h2><?php echo $text_your_password; ?></h2>
    <div class="content">
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $entry_password; ?></td>
          <td><input type="password" name="password" value="<?php echo $password; ?>" />
            <?php if ($error_password) { ?>
            <span class="error"><?php echo $error_password; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_confirm; ?></td>
          <td><input type="password" name="confirm" value="<?php echo $confirm; ?>" />
            <?php if ($error_confirm) { ?>
            <span class="error"><?php echo $error_confirm; ?></span>
            <?php } ?></td>
        </tr>
      </table>
    </div>
    <h2><?php echo $text_newsletter; ?></h2>
    <div class="content">
      <table class="form">
        <tr>
          <td><?php echo $entry_newsletter; ?></td>
          <td><?php if ($newsletter == 1) { ?>
            <input type="radio" name="newsletter" value="1" checked="checked" />
            <?php echo $text_yes; ?>
            <input type="radio" name="newsletter" value="0" />
            <?php echo $text_no; ?>
            <?php } else { ?>
            <input type="radio" name="newsletter" value="1" />
            <?php echo $text_yes; ?>
            <input type="radio" name="newsletter" value="0" checked="checked" />
            <?php echo $text_no; ?>
            <?php } ?></td>
        </tr>
      </table>
    </div>
    <?php if ($text_agree) { ?>
    <div class="buttons">
      <div class="right"><?php echo $text_agree; ?>
        <?php if ($agree) { ?>
        <input type="checkbox" name="agree" value="1" checked="checked" />
        <?php } else { ?>
        <input type="checkbox" name="agree" value="1" />
        <?php } ?>
        <input type="submit" value="<?php echo $button_continue; ?>" class="button" />
      </div>
    </div>
    <?php } else { ?>
    <div class="buttons">
      <div class="right">
        <input type="submit" value="<?php echo $button_continue; ?>" class="button" />
      </div>
    </div>
    <?php } ?>
  </form>
  <?php echo $content_bottom; ?></div>
<?php if ($customer_groups) { ?>
<script type="text/javascript"><!--
$('#button-account').bind('click', function() {
	html  = '<h2><?php echo $text_your_account; ?></h2>';     
	html += '<div class="content">';
	html += '  <p><?php echo $text_account_type; ?></p>';
	html += '  <table class="radio">';
	<?php foreach ($customer_groups as $customer_group) { ?>
	
	html += '    <tr class="highlight">';
	html += '      <td style="vertical-align: top;">';
	
	<?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
	html += '<input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer-group<?php echo $customer_group['customer_group_id']; ?>" checked="checked" />';
	<?php } else { ?>
	html += '<input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer-group<?php echo $customer_group['customer_group_id']; ?>" />';
	<?php } ?>
	
	html += '      </td>';
	html += '      <td style="vertical-align: top;"><label for="customer-group<?php echo $customer_group['customer_group_id']; ?>"><b><?php echo $customer_group['name']; ?></b></label>';
	
	<?php if ($customer_group['description']) { ?>
	html += '      <label for="customer-group<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['description']; ?></label>';
	<?php } ?>
	
	html += '    <br /></td>';
	html += '  </tr>';
	<?php } ?>
	html += '  </table>';
	html += '</div>';
	
	$.colorbox({
		overlayClose: true,
		opacity: 0.5,
		width: '600px',
		height: '400px',
		href: false,
		html: html
	});
})
//--></script>
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
$('select[name=\'country_id\']').bind('change', function() {
	var postcode_required = [<?php echo implode(',', $postcode_required_data); ?>];
	
	if ($.inArray(this.value, postcode_required) >= 0) {
		$('#postcode-required').show();
	} else {
		$('#postcode-required').hide();
	}
	
	$('select[name=\'zone_id\']').load('index.php?route=account/register/zone&country_id=' + this.value + '&zone_id=<?php echo $zone_id; ?>');
});

$('select[name=\'country_id\']').trigger('change');
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
$('input[name=\'customer_group_id\']').live('click', function() {
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

$('input[name=\'customer_group_id\']:checked').trigger('click');
//--></script> 
<script type="text/javascript"><!--
$('.colorbox').colorbox({
	width: 640,
	height: 480
});
//--></script> 
<?php echo $footer; ?>