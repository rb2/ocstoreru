<form action="<?php echo $action; ?>" method="post" id="checkout">
	<input type="hidden" name="eshopId" value="<?php echo $eshopId; ?>">
	<input type="hidden" name="orderId" value="<?php echo $orderId; ?>">
	<input type="hidden" name="serviceName" value="<?php echo $serviceName; ?>">
	<input type="hidden" name="recipientAmount" value="<?php echo $recipientAmount; ?>">
	<input type="hidden" name="recipientCurrency" value="<?php echo $recipientCurrency; ?>">
	<input type="hidden" name="successUrl" value="<?php echo $successUrl; ?>">
	<input type="hidden" name="failUrl" value="<?php echo $failUrl; ?>">
</form>
<div class="buttons">
  <table>
    <tr>
      <td align="left"><a onclick="location = '<?php echo $back; ?>'" class="button"><span><?php echo $button_back; ?></span></a></td>
      <td align="right"><a onclick="$('#checkout').submit();" class="button"><span><?php echo $button_confirm; ?></span></a></td>
    </tr>
  </table>
</div>