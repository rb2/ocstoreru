<div class="box">
  <div class="top"><img src="catalog/view/theme/default/image/bestsellers.png" alt="" /><?php echo $heading_title; ?></div>
  <div id="information" class="middle">
    <?php if ($links) { ?>
    <ul>
    	<?php foreach($links as $link) { ?>
    	<li><?php echo $link['alink']; ?></li>
    	<?php } ?>
    </ul>
    <?php } ?>
  </div>
  <div class="bottom">&nbsp;</div>
</div>
