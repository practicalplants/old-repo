<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package Toolbox
 * @since Toolbox 0.1
 */
?>

	</div><!-- #main -->

<?php
$masthead = PracticalPlants_Masthead::getInstance();
$masthead->footer(); ?>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>