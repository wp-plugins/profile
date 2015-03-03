<?php
/**
 * Use dasboard template
 *
 * User profile landing page
 *
 * @link http://wp3.in
 * @since 0.0.1
 *
 * @package Profile
 */

?>
<?php include profile_get_theme_location('user-card.php'); ?>
<div id="profile-page" class="es-main-wrapper clearfix" data-id="<?php echo get_current_user_id(); ?>">
	<div class="profile-head clearfix">

	</div>
	<div class="row">
		<div class="left-navbar col-md-3">
			<?php profile_navigation() ?>
		</div>
		<div class="profile-page_c col-md-9">
			<?php
                profile_blocks(); 
            ?>
		</div>
	</div>
</div>