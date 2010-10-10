<div style="background: #f7f7f7; border: 1px solid #dddddd; padding: 10px; margin-bottom: 10px;">
<?php echo $text_instruction; ?><br /><br />

<a href="index.php?route=payment/fl_sberbank/printpay" class="button" style="text-decoration:none;"><span><?php echo $text_printpay; ?></span></a>

  <br /><br />
  <?php echo $text_payment; ?></div>
 

<div class="buttons">
  <table>
    <tr>
      <td align="left"><a onclick="location='<?php echo $back; ?>'" class="button"><span><?php echo $button_back; ?></span></a></td>
      <td align="right"><a id="checkout" class="button"><span><?php echo $button_confirm; ?></span></a></td>
    </tr>
  </table>
</div>
<script type="text/javascript"><!--
$('#checkout').click(function() {
	$.ajax({ 
		type: 'get',
		url: 'index.php?route=payment/fl_sberbank/confirm',
		success: function() {
			location = '<?php echo $continue; ?>';
		}		
	});
});
//--></script>
