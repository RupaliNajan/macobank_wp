<?php 
/*Template Name:Contact Us*/
get_header();
?>
<div class="container">
	<h3 style="color: green"><b><center>contact Us</center></b></h3>
</div>
<div class="contact">
<?php echo do_shortcode('[wpforms id="119"]');?>
</div>
</div>
<?php
get_footer();
?>