<div class="content">
<p><?php echo $sub_text_info; ?></p>
<form action="<?php echo $action; ?>" method="get" id="checkout">
	<input type="hidden" name="from" value="<?php echo $from; ?>">
	<input type="hidden" name="summ" value="<?php echo $summ; ?>">
	<input type="hidden" name="com" value="<?php echo $com; ?>">
	<input type="hidden" name="txn_id" value="<?php echo $txn_id; ?>">
	<div style="text-align: right;"><?php echo $sub_text_info_phone; ?> <input type="text" name="to" value="" size="10"></div>
</form>
</div>
<div class="buttons">
  <table>
    <tr>
      <td align="left"><a onclick="location = '<?php echo $back; ?>'" class="button"><span><?php echo $button_back; ?></span></a></td>
      <td align="right"><a onclick="$('#checkout').submit();" class="button"><span><?php echo $button_confirm; ?></span></a></td>
    </tr>
  </table>
</div>