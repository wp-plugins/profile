<?php

/**
 * Favorites
 *
 * Shows the lists of posts favourited by a user
 *
 * @link http://wp3.in
 * @since 0.0.1
 *
 * @package Profile
 */
?>
<div id="favorite-page" class="clearfix" data-id="<?php echo get_current_user_id(); ?>">
	<div class="row">
		<div class="left-navbar col-md-3">
			<?php
			profile_navigation() ?>
		</div>
		<div class="profile-page_c col-md-9">
			<?php
				$posts = profile_user_favorite_post_query(profile_get_current_user());

				if ($posts->have_posts()) {
				?>
				<h3 class="profile-list-head">
					<?php the_title() ?>
					<span class="user-post-count">(<?php echo wp3_user_favorites_count(profile_get_current_user()) ?>)</span>
					<span class="cpts-links pull-right"><?php profile_user_favorite_cpt_links() ?></span>
				</h3>
					<?php
					while ($posts->have_posts()):
						$posts->the_post();
					include profile_get_theme_location('content-post.php');
					endwhile;
				} 
				else {
					_e('No posts were written by this user.', 'profile');
				}
				wp_reset_postdata();
			?>
		</div>
	</div>
</div>