<?php
/**
 * Plugin acivation hook
 *
 * Things to do after activating profile plugin
 *
 * @link http://wp3.in
 * @since 0.0.1
 *
 * @package Profile
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function profile_activate($network_wide) {

    // add roles
    //$ap_roles = new PROFILE_Roles;
    //$ap_roles->add_roles();
    //$ap_roles->add_capabilities();
    
    
    global $wpdb;

        
    // check if page already exists
    $page_id = profile_opt("base_page");
    
    $post = get_post($page_id);
    
    if (!$post) {
        $args = array();
        $args['post_type']          = "page";
        $args['post_content']       = "[profile]";
        $args['post_status']        = "publish";
        $args['post_title']         = "PROFILE_TITLE";
        $args['post_name']          = "profile";
        $args['comment_status']     = 'closed';
        
        // now create post
        $new_page_id = wp_insert_post($args);
    
        if ($new_page_id) {
            $page = get_post($new_page_id);
            profile_opt("base_page", $page->ID);
            profile_opt("base_page_id", $page->post_name);
        }
    }

    
    
    
    if (profile_opt('version') != PROFILE_VERSION) {
        profile_opt('installed', false);
        profile_opt('version', PROFILE_VERSION);
    }
    
    /**
     * Run DB quries only if PROFILE_DB_VERSION does not match
     */
    if (profile_opt('db_version') != PROFILE_DB_VERSION) {   
    
        $charset_collate = !empty($wpdb->charset) ? "DEFAULT CHARACTER SET ".$wpdb->charset : '';

        $meta_table = "CREATE TABLE IF NOT EXISTS `".$wpdb->base_prefix."ap_meta` (
                  `profile_id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `profile_userid` bigint(20) DEFAULT NULL,
                  `profile_type` varchar(256) DEFAULT NULL,
                  `profile_actionid` bigint(20) DEFAULT NULL,
                  `profile_value` text,
                  `profile_param` LONGTEXT DEFAULT NULL,
                  `profile_date` timestamp NULL DEFAULT NULL,
                  PRIMARY KEY (`profile_id`)
                )".$charset_collate.";";
        
        require_once(ABSPATH.'wp-admin/includes/upgrade.php');
        dbDelta($meta_table);
        profile_opt('db_version', PROFILE_DB_VERSION);
    }

    
    if (!get_option('profile_opt')) {
            update_option('profile_opt', profile_default_options());
    } else {
            update_option('profile_opt', get_option('profile_opt') + profile_default_options());
    }
        
    
    profile_opt('ap_flush', 'true'); 
    flush_rewrite_rules(false);
}
