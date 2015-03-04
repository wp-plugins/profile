<?php

/**
 * This file contains theme script, styles and other theme related functions.
 *
 * This file can be overridden by creating a anspress directory in active theme folder.
 *
 * @package    AnsPress
 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author    Rahul Aryan <rah12@live.com>
 */

/**
 * Enqueue scripts
 *
 */
add_action('wp_enqueue_scripts', 'init_profile_assets', 11);
function init_profile_assets() {
    ?>
    <script type="text/javascript"> var ajaxurl = '<?php
        echo admin_url('admin-ajax.php'); ?>'; </script>
        <?php
        wp_enqueue_script('profile_script', PROFILE_URL . 'assets/profile_site.js', 'jquery', PROFILE_VERSION);
        
        wp_localize_script('profile_script', 'profilelang', array(
            'password_field_not_macthing' => __('Password not matching', 'profile') ,
            'error' => '',
            ));
        
        wp_enqueue_style('profile-fonts', profile_get_theme_url('fonts/style.css') , array() , PROFILE_VERSION);
        wp_enqueue_style('profile-style', profile_get_theme_url('css/main.min.css') , array() , PROFILE_VERSION);
        
        //if (is_profile()) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-form', array(
                'jquery'
                ) , false, true);
            wp_enqueue_script('tooltipster', profile_get_theme_url('js/jquery.tooltipster.min.js') , 'jquery', PROFILE_VERSION);
            wp_enqueue_script('profile-theme-js', profile_get_theme_url('js/profile-theme.js') , 'jquery', PROFILE_VERSION);
            wp_enqueue_script('initial', profile_get_theme_url('js/initial.min.js') , 'jquery', PROFILE_VERSION);
            do_action('profile_enqueue');
            wp_enqueue_style('profile-overrides', profile_get_theme_url('css/overrides.css') , array() , PROFILE_VERSION);
       // }
    }

/*add_action( 'widgets_init', 'profile_widgets_positions' );
function profile_widgets_positions(){
	register_sidebar( array(
		'name'         	=> __( 'AP Before', 'ap' ),
		'id'           	=> 'ap-before',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' 	=> '</div>',
		'description'  	=> __( 'Widgets in this area will be shown before anspress body.', 'ap' ),
		'before_title' 	=> '<h3 class="ap-widget-title">',
		'after_title'  	=> '</h3>',
	) );

}*/
