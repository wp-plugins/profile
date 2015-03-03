<?php

/**
 * Profile dashboard page
 * @link http://wp3.in/anspress
 * @since 0.0.1
 * @package Profile
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
	<?php screen_icon(); echo '<h2>'.__('Profile Dashboard').'</h2>';
    // This shows the page's name and an icon if one has been provided ?>
			
	<?php printf(__('For help and support visit %s.', 'profile'), '<a href="http://wp3.in" target="_blank">WP3 Support</a>') ?>
	
</div>