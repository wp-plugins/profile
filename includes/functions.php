<?php

/**
 * Common Profile functions
 *
 * @link http://wp3.in
 * @since 0.0.1
 * @package Profile
 */

/**
 * Get slug of base page
 * @return string
 * @since 2.0.0-beta
 */
function profile_base_page_slug() {
    $base_page = get_post(profile_opt('base_page'));
    
    $slug      = $base_page->post_name;
    
    return apply_filters('profile_base_page_slug', $slug);
}

/**
 * Retrive permalink to base page
 * @return string URL to Profile base page
 * @since 2.0.0-beta
 */
function profile_base_page_link() {
    return get_permalink(profile_opt('base_page'));
}

function profile_theme_list() {
    $themes = array();
    $dirs   = array_filter(glob(PROFILE_THEME_DIR . '/*') , 'is_dir');
    foreach ($dirs as $dir) $themes[basename($dir) ]        = basename($dir);
    return $themes;
}

function profile_get_theme() {
    $option = profile_opt('theme');
    if (!$option) return 'default';
    
    return profile_opt('theme');
}

/**
 * Get location to a file
 * First file is looked inside active WordPress theme directory /anspress.
 * @param   string      $file       file name
 * @param   mixed       $plugin     Plugin path
 * @return  string
 * @since   0.1
 */
function profile_get_theme_location($file, $plugin        = false) {

    // checks if the file exists in the theme first,
    // otherwise serve the file from the plugin
    if ($theme_file    = locate_template(array(
        'anspress/' . $file
        ))) {
        $template_path = $theme_file;
} 
elseif ($plugin !== false) {
    $template_path = $plugin . '/theme/' . $file;
} 
else {
    $template_path = PROFILE_THEME_DIR . '/' . profile_get_theme() . '/' . $file;
}
return $template_path;
}

/**
 * Get url to a file
 * Used for enqueue CSS or JS
 * @param       string  $file
 * @param       mixed $plugin
 * @return      string
 * @since       2.0
 */
function profile_get_theme_url($file, $plugin       = false) {

    // checks if the file exists in the theme first,
    // otherwise serve the file from the plugin
    if ($theme_file   = locate_template(array(
        'profile/' . $file
        ))) {
        $template_url = get_template_directory_uri() . '/profile/' . $file;
} 
elseif ($plugin !== false) {
    $template_url = $plugin . 'theme/' . $file;
} 
else {
    $template_url = PROFILE_THEME_URL . '/' . profile_get_theme() . '/' . $file;
}
return $template_url;
}

/**
 * @param string $sub
 */
function profile_get_link_to($sub, $user_id    = false) {

    if (!$user_id) $user_id    = profile_get_current_user();
    
    $user_login = get_the_author_meta('user_login');
    
    $base       = rtrim(get_permalink(profile_opt('base_page')) , '/');
    
    if (get_option('permalink_structure') != '') {
        $args       = '/' . $user_login;
        
        if (!is_array($sub)) $args.= $sub ? '/' . $sub : '';
        elseif (is_array($sub)) {
            $args.= '/';
            
            if (!empty($sub)) foreach ($sub as $s) $args.= $s . '/';
        }
        
        $link = $base;
    } 
    else {
        $args = '&profile_user=' . $user_id;
        
        if (!is_array($sub)) $args.= $sub ? '&profile_page=' . $sub : '';
        elseif (is_array($sub)) {
            $args.= '';
            
            if (!empty($sub)) foreach ($sub as $k    => $s) $args.= '&' . $k . '=' . $s;
        }
        
        $link = $base;
    }
    
    return $link . $args;
}

/**
 * Append array to global var
 * @param  string   $key
 * @param  array    $args
 * @param string    $var
 * @return void
 * @since 2.0.0-alpha2
 */
function profile_append_to_global_var($var, $key, $args, $group = false) {
    if (!isset($GLOBALS[$var])) $GLOBALS[$var]       = array();
    
    if (!$group) $GLOBALS[$var][$key]       = $args;
    else $GLOBALS[$var][$group][$key]       = $args;
}

/**
 * Register user page
 * @param  string $page_slug  slug for links
 * @param  string $page_title Page title
 * @param  callable $func Hook to run when shortcode is found.
 * @return void
 * @since 2.0.1
 */
function profile_register_user_page($page_slug, $page_title, $func) {
    profile_append_to_global_var('user_pages', $page_slug, array(
        'title' => $page_title,
        'func' => $func
        ));
}

/**
 * Check if current page profile base page
 * @return boolean
 * @since 0.0.1
 */
function is_profile() {
    if (get_the_ID() == profile_opt('base_page')) return true;
    return false;
}

/**
 * Get current user of profile page
 * @return intgere
 * @since 0.0.1
 */
function profile_get_current_user() {
    GLOBAL $profile_user_id;
    $profile_user_id = (int)get_query_var('profile_user');
    
    if (empty($profile_user_id)) return get_current_user_id();
    
    return $profile_user_id;
}

function profile_current_user_object() {
    global $profile_user_obj;
    
    $profile_user_obj = get_user_by('id', profile_get_current_user());
    return $profile_user_obj;
}

/**
 * Check if current profile is my
 * @return boolean
 * @since 0.0.1
 */
function is_my_profile() {

    if (!is_profile()) return false;
    
    if (profile_get_current_user() == get_current_user_id()) return true;
    
    return false;
}


/**
 * Register profile option tab and fields
 * @param  string   $group_slug     slug for links
 * @param  string   $group_title    Page title
 * @param  array    $fields         fields array.
 * @return void
 * @since 0.0.1
 */
function profile_register_option_group($group_slug, $group_title, $fields) {
    profile_append_to_global_var('profile_option_tabs', $group_slug, array(
        'title' => $group_title,
        'fields' => $fields
        ));
}

/**
 * Output option tab nav
 * @return void
 * @since 2.0.0-alpha2
 */
function profile_options_nav() {
    global $profile_option_tabs;
    $active = (isset($_REQUEST['option_page'])) ? $_REQUEST['option_page'] : 'general';
    
    $menus  = array();
    
    foreach ($profile_option_tabs as $k      => $args) {
        $link   = admin_url("admin.php?page=profile_options&option_page={$k}");
        $menus[$k]        = array(
            'title'        => $args['title'],
            'link'        => $link
            );
    }
    
    /**
     * FILTER: profile_option_tab_nav
     * filter is applied before showing option tab navigation
     * @var array
     * @since  0.0.1
     */
    $menus  = apply_filters('profile_option_tab_nav', $menus);
    
    $o      = '<ul id="profile_opt_nav" class="nav nav-tabs">';
    foreach ($menus as $k      => $m) {
        $class  = !empty($m['class']) ? ' ' . $m['class'] : '';
        $o.= '<li' . ($active == $k ? ' class="active"' : '') . '><a href="' . $m['link'] . '" class="ap-user-menu-' . $k . $class . '">' . $m['title'] . '</a></li>';
    }
    $o.= '</ul>';
    
    echo $o;
}

/**
 * Display fields group options. Uses AnsPress_Form to renders fields.
 * @return void
 * @since 0.0.1
 */
function profile_option_group_fields() {
    global $profile_option_tabs;
    $active = (isset($_REQUEST['option_page'])) ? sanitize_text_field($_REQUEST['option_page']) : 'general';
    
    if (empty($profile_option_tabs) && is_array($profile_option_tabs)) return;
    
    $fields = $profile_option_tabs[$active]['fields'];
    
    $fields[]        = array(
        'name'        => 'action',
        'type'        => 'hidden',
        'value'        => 'profile_save_options',
        );
    
    $args   = array(
        'name'        => 'options_form',
        'is_ajaxified'        => true,
        'submit_button'        => __('Save options', 'profile') ,
        'nonce_name'        => 'nonce_option_form',
        'fields'        => $fields,
        );
    
    $form   = new Wp3_Form($args);
    
    echo $form->get_form();
}

/**
 * Sort array by order value. Group array which have same order number and then sort them.
 * @param  array $array
 * @return array
 * @since 0.0.1
 */
if (!function_exists('wp3_sort_array_by_order')):
    function wp3_sort_array_by_order($array) {
        $new_array = array();
        if (!empty($array) && is_array($array)) {
            $group     = array();
            foreach ($array as $k         => $a) {
                $order     = $a['order'];
                $group[$order][]           = $a;
                $group[$order]['order']           = $order;
            }
            
            usort($group, function ($a, $b) {
                return $a['order'] - $b['order'];
            });
            
            foreach ($group as $a) {
                foreach ($a as $k => $newa) {
                    if ($k !== 'order') $new_array[]   = $newa;
                }
            }
            
            return $new_array;
        }
    }
    endif;

/**
 * Resize image and save it in upload dir
 * @param  integer $size
 * @param  boolean $default
 * @return string
 * @since  0.0.1
 */
function profile_get_avatar($user_id, $size       = 'thumbnail', $default    = false) {
    $upload_dir = wp_upload_dir();
    
    if ($default) {
        $image      = wp_get_attachment_image_src(profile_opt('default_avatar') , 'thumbnail');
    } 
    else {
        $image      = wp_get_attachment_image_src(get_user_meta($user_id, '__profile_avatar', true) , array(
            $size,
            $size
            ));
    }
    
    if ($image === false || !is_array($image) || empty($image[0])) {
        return false;
    }
    
    return $image[0];
}

/**
 * Get the meta of current user
 * @param  string $key meta key
 * @param  boolean $single return only single value instead of array
 * @return mixed
 * @since  0.0.1
 */
function profile_current_user_meta($key, $single = false) {
    global $profile_user_meta;

    $profile_user_meta = get_user_meta(profile_get_current_user());

    $meta   = (array)$profile_user_meta;
    
    /*if($key == 'followers')
    return @$meta[PROFILE_FOLLOWERS_META] ? $meta[ES_FOLLOWERS_META] : 0;
    
    elseif($key == 'following')
    return @$meta[ES_FOLLOWING_META] ? $meta[ES_FOLLOWING_META] : 0;*/
    
    if (isset($meta[$key])) {
        if ($single) return $meta[$key][0];
        
        return $meta[$key];
    }
    
    return false;
}

/**
 * For user display name
 * It can be filtered for adding cutom HTML
 * @param  mixed $args
 * @return string
 * @since 0.0.1
 */
function profile_display_name($args     = array()) {
    global $post;
    $defaults = array(
        'user_id'          => get_the_author_meta('ID') ,
        'html'          => false,
        'echo'          => false,
        'anonymous_label'          => __('Anonymous', 'profile') ,
        );
    
    if (!is_array($args)) {
        $defaults['user_id']          = $args;
        $args     = $defaults;
    } 
    else {
        $args     = wp_parse_args($args, $defaults);
    }
    
    extract($args);
    
    $return = '';
    
    if ($user_id > 0) {
        $user   = get_userdata($user_id);
        
        if (!$html) {
            $return = $user->display_name;
        } 
        else {
            $return = '<span class="who"><a href="' . profile_user_link($user_id) . '">' . $user->display_name . '</a></span>';
        }
    } 
    elseif ($post->post_type == 'question' || $post->post_type == 'answer') {
        $name   = get_post_meta($post->ID, 'anonymous_name', true);
        
        if (!$html) {
            if ($name != '') {
                $return = $name;
            } 
            else {
                $return = $anonymous_label;
            }
        } 
        else {
            if ($name != '') {
                $return = '<span class="who">' . $name . __(' (anonymous)', 'profile') . '</span>';
            } 
            else {
                $return = '<span class="who">' . $anonymous_label . '</span>';
            }
        }
    } 
    else {
        $return = '<span class="who">' . $anonymous_label . '</span>';
    }
    
    /**
     * FILTER: profile_display_name
     * Filter can be used to alter display name
     * @var string
     * @since 0.0.1
     */
    if (!$html) $return = apply_filters('profile_display_name', $return);
    else $return = apply_filters('profile_display_name_html', $return);
    
    if ($echo) {
        echo $return;
    } 
    else {
        return $return;
    }
}

/**
 * Return response with type and message
 * @param  string $id messge id
 * @param  boolean $only_message return message string instead of array
 * @return string
 * @since 2.0.0-alpha2
 */
function profile_responce_message($id, $only_message = false) {
    $msg          = array(
        'success'              => array(
            'type'              => 'success',
            'message'              => __('Current action executed successfully.', 'profile')
            ) ,
        'please_login'              => array(
            'type'              => 'warning',
            'message'              => __('You need to login before doing this action.', 'profile')
            ) ,
        'something_wrong'              => array(
            'type'              => 'error',
            'message'              => __('Something went wrong, last action failed.', 'profile')
            ) ,
        'no_permission'              => array(
            'type'              => 'warning',
            'message'              => __('You do not have permission to do this action.', 'profile')
            ) ,
        'form_error'              => array(
            'type'              => 'error',
            'message'              => __('Form is not submitted, check fields again.', 'profile')
            ) ,
        'updated_user_field'              => array(
            'type'              => 'success',
            'message'              => __('Successfully updated the field.', 'profile')
            ) ,
        'avatar_uploaded'              => array(
            'type'              => 'success',
            'message'              => __('Successfully updated your avatar.', 'profile')
            ) ,
        'added_to_favorite'              => array(
            'type'              => 'success',
            'message'              => __('Successfully added to your favorite list.', 'profile')
            ) ,
        'removed_from_favorite'              => array(
            'type'              => 'success',
            'message'              => __('Successfully removed from your favorite list.', 'profile')
            ) ,
        );

    /**
     * FILTER: profile_responce_message
     * Can be used to alter response messages
     * @var array
     * @since 2.0.1
     */
    $msg          = apply_filters('profile_responce_message', $msg);
    
    if (isset($msg[$id]) && $only_message) return $msg[$id]['message'];
    
    if (isset($msg[$id])) return $msg[$id];
    
    return false;
}

function profile_ajax_responce($results) {

    if (!is_array($results)) {
        $message_id    = $results;
        $results       = array();
        $results['message']               = $message_id;
    }
    
    $results['profile_responce']               = true;
    
    if (isset($results['message'])) {
        $error_message = profile_responce_message($results['message']);
        
        if ($error_message !== false) {
            $results['message']               = $error_message['message'];
            $results['message_type']               = $error_message['type'];
        }
    }
    
    /**
     * FILTER: profile_ajax_responce
     * Can be used to alter profile_ajax_responce
     * @var array
     * @since 2.0.1
     */
    $results       = apply_filters('profile_ajax_responce', $results);
    
    return $results;
}

function profile_send_json($results = array()) {
    $results['is_profile_ajax']         = true;
    
    wp_send_json(profile_ajax_responce($results));
}

function profile_have_fields() {
    $count = wp_count_posts('profile_field');
    
    if ($count->publish > 0) return true;
    
    return false;
}

/* retrieve the visibility of group */
function profile_get_label_visibility($term_id) {
    $tax_meta = get_option("profile_group_$term_id");
    
    return $tax_meta['visibility'];
}

function profile_user_favorite_post_query($user_id) {
    $cpts      = array_keys(profile_opt('favorite_cpt'));
    $post_type = sanitize_text_field(get_query_var('profile_cpt'));
    $post_type = in_array($post_type, $cpts) ? $post_type : $cpts;

    $args      = array(
        'author' => $user_id,
        'profile_query' => 'user_favorites',
        'post_type' => $post_type,
        'post_status' => 'publish',
        'paged' => $paged,
        'orderby' => 'date',
        'order' => 'DESC'
        );
    return new WP_Query($args);
}
