<?php

/**
 * Profile options
 *
 * @package   Profile
 * @author    Rahul Aryan <admin@rahularyan.com>
 * @license   GPL-2.0+
 * @link      http://rahularyan.com
 * @copyright 2014 Rahul Aryan
 * @since 2.0.1
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * To retrive profile option
 * @param  string $key   Name of option to retrive,
 *                       Keep it blank to get all options of Profile
 * @param  string $value Enter value to update existing option
 * @return string
 * @since 0.1
 */
function profile_opt($key      = false, $value    = false) {
    $settings = wp_cache_get('profile_opt', 'options');
    
    if ($settings === false) {
        $settings = get_option('profile_opt');
        if (!$settings) $settings = array();
        $settings = $settings + profile_default_options();
        
        wp_cache_set('profile_opt', $settings, 'options');
    }
    if ($value) {
        
        $settings[$key] = $value;
        update_option('profile_opt', $settings);
        
        // clear cache if option updated
        wp_cache_delete('profile_opt', 'options');
        
        return;
    }
    
    if (!$key) return $settings;
    
    if (isset($settings[$key])) return $settings[$key];
    else return NULL;
    
    return false;
}

/**
 * Default options for profile
 * @return array
 * @since 2.0.1
 */
function profile_default_options() {
    $defaults = array(
        'main_avatar_size'          => 250,
        'favorite_cpt'          => array(
            'post'
        ) ,
    );
    
    /**
     * FILTER: profile_default_options
     * Filter to be used by extensions for including their default options.
     * @var array
     * @since 0.1
     */
    $defaults = apply_filters('profile_default_options', $defaults);
    
    return $defaults;
}

function profile_allowed_editor_tags() {
    $tags = array(
        'a' => array(
            'href' => array() ,
            'title' => array()
        ) ,
        'br' => array() ,
        'em' => array() ,
        'strong' => array() ,
        'ul' => array() ,
        'li' => array() ,
    );
    
    return apply_filters('profile_allowed_editor_tags', $tags);
}
