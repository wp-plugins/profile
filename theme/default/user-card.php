<?php
/**
 * Use card
 *
 * Display the user details in profile page
 *
 * @link http://wp3.in
 * @since 0.0.2
 *
 * @package Profile
 */
?>
<div id="user-card" class="row">
	<div class="col-md-3">
		<div class="profile-main-avatar">
			<div class="block-center">
				<?php echo get_avatar(profile_get_current_user(), profile_opt('main_avatar_size')); ?>
				<?php profile_avatar_upload_form() ?>
			</div>
		</div>
	</div>
	<div class="col-md-9">
		<div class="col-md-8 about">
			<?php profile_user_about_card() ?>
		</div>
		<div class="col-md-4 user-links">
			<?php profile_user_link_card() ?>
		</div>
	</div>	
</div>