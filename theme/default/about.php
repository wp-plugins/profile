<?php
/**
 * About page
 *
 * User's about page, whwre user can edit his informations too
 *
 * @link http://wp3.in
 * @since 0.0.1
 *
 * @package Profile
 */

?>

<div id="about-page" class="es-main-wrapper clearfix" data-id="<?php echo get_current_user_id(); ?>">
	<div class="profile-head clearfix">

	</div>
	<div class="row">
		<div class="left-navbar col-md-3">
			<?php profile_navigation() ?>
		</div>
		<div class="profile-page_c col-md-9">
			<div class="user-metas">
				<?php profile_display_all_metas() ?>
			</div>
		</div>
	</div>
</div>