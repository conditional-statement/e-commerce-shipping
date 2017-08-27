<?php $objBasket = is_object($objBasket) ? $objBasket : new Basket(); ?>
<h2>Your Basket</h2>
<dl id="basket_left">
	<dt>No. of items:</dt>
	<dd class="bl_ti"><span><?php echo $objBasket->_number_of_items; ?></span></dd>
	<dt>Sub-total:</dt>
	<dd class="bl_st">&pound;<span><?php echo number_format($objBasket->_sub_total, 2); ?></span></dd>
</dl>
<div class="dev br_td">&#160;</div>
<p><a href="<?php echo $this->objUrl->href('basket'); ?>">View Basket</a> | <a href="<?php echo $this->objUrl->href('checkout'); ?>">Checkout</a></p>
<div class="dev br_td">&#160;</div>