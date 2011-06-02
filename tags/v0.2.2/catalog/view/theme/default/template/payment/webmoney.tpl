<form action="<?php echo $action; ?>" accept-charset="cp1251" method="post" id="checkout">
	<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="<?php echo $amount; ?>">
	<input type="hidden" name="LMI_PAYMENT_DESC" value="<?php echo $description; ?>">
	<input type="hidden" name="LMI_PAYMENT_NO" value="<?php echo $order_id; ?>">
	<input type="hidden" name="LMI_PAYEE_PURSE" value="<?php echo $LMI_PAYEE_PURSE; ?>">
</form>
<div class="buttons">
  <table>
    <tr>
      <td align="left"><a onclick="location = '<?php echo $back; ?>'" class="button"><span><?php echo $button_back; ?></span></a></td>
      <td align="right"><a onclick="$('#checkout').submit();" class="button"><span><?php echo $button_confirm; ?></span></a></td>
    </tr>
  </table>
</div>