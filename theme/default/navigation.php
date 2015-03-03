<?php
/**
 * Main navigation of profile
 *
 * User profile navigation page
 *
 * @link http://wp3.in
 * @since 0.0.1
 *
 * @package Profile
 */

/**
 * global 
 */
global $profile_navigation;
?>
<ul id="profile-navigation" class="clearfix">
	<?php foreach ($profile_navigation as $k => $args) : ?>
		<li<?php echo !empty($args['class']) ? ' class="'.$args['class'].'"' : '' ?>>
		<a href="<?php echo $args['link'] ?>"><?php echo $args['title'] ?><i class="profileicon-chevron-right"></i></a>
	</li>
<?php endforeach; ?>
</ul>