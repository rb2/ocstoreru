<div id="payment" class="content">
  <table class="form">
    <tr>
      <td><?php echo $entry_gender; ?></td>
      <td><select name="gender">
          <option value="M"><?php echo $text_male; ?></option>
          <option value="F"><?php echo $text_female; ?></option>
        </select></td>
    </tr>
    <tr>
      <td><?php echo $entry_dob; ?></td>
      <td><input type="text" name="dob" value="" /></td>
    </tr>
    <tr>
      <td><?php echo $entry_house_no; ?></td>
      <td><input type="text" name="house_no" value="" /></td>
    </tr>
    <tr>
      <td><?php echo $entry_house_ext; ?></td>
      <td><input type="text" name="house_ext" value="" /></td>
    </tr>
  </table>
</div>
<div class="buttons">
  <div class="right">
    <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
  </div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({
		url: 'index.php?route=payment/klarna_invoice/send',
		type: 'post',
		data: $('#payment :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm').attr('disabled', true);
			
			$('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
				
				$('#button-confirm').attr('disabled', false);
			}
			
			$('.attention').remove();
			
			if (json['success']) {
				location = json['success'];
			}
		}
	});
});
//--></script> 
