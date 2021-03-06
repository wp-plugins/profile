<?php
/**
 * posts loop template
 *
 * @link http://wp3.in
 * @since 0.0.1
 *
 * @package Profile
 */
$type = get_post_type( );
?>
<div class="user-post question clearfix">
	<a class="user-qa-type <?php echo $type ?>" href="<?php the_permalink() ?>"><?php echo $type == 'question' ? __('Q', 'profile') : __('A', 'profile') ?></a>
	<span class="user-qa-vote" title="<?php _e('Votes', 'profile') ?>"><?php echo ap_net_vote_meta(); ?></span>
	<a href="<?php the_permalink() ?>" class="user-post-title"><?php the_title(); ?></a>
	<time><?php echo get_the_date( 'M d \'y' ); ?></time>
</div>