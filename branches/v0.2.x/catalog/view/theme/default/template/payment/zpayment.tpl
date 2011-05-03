<form action="<?php echo $action; ?>" accept-charset="cp1251" method="post" id="checkout">
	<input name="LMI_PAYMENT_NO" type="hidden" value="<?php echo $order_id; ?>" />
	<input name="LMI_PAYMENT_AMOUNT" type="hidden" value="<?php echo $amount; ?>" />
	<input name="CLIENT_MAIL" type="hidden" value="<?php echo $email; ?>" />
	<input name="LMI_PAYMENT_DESC" type="hidden" value="<?php echo $description; ?>" />
	<input name="LMI_PAYEE_PURSE" type="hidden" value="<?php echo $shop_id; ?>" />
	<input name="ZP_SIGN" type="hidden" value="<?php echo md5($shop_id . $order_id . $amount . $init_password); ?>" />
</form>
<div class="buttons">
  <table>
    <tr>
      <td align="left"><a onclick="location = '<?php echo $back; ?>'" class="button"><span><?php echo $button_back; ?></span></a></td>
      <td align="right"><a onclick="$('#checkout').submit();" class="button"><span><?php echo $button_confirm; ?></span></a></td>
    </tr>
  </table>
</div>