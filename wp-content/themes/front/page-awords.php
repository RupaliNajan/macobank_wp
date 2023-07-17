<?php
/*Template Name:Awards*/
 get_header();
?>

<div class="container">
<h2 style="color: #900C3F"><b><?php the_title();?></b></h2>
<h3 style="color: green"><center>Awards</center></h3>
<div class="new_section">
	<div><img src="<?php echo get_template_directory_uri();?>/images/Award1.jpg"></div><br><br>
	<div><img src="<?php echo get_template_directory_uri();?>/images/Award2.jpg"></div>
</div>
</div>

<?php
  get_footer();
?>