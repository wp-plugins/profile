<?php



function wp3_insert_profile_views() {
    if (!is_profile() || profile_get_current_user() == 0)
        return;

    $userid = get_current_user_id();
    
    $row = wp3_add_meta($userid, 'profile_view', profile_get_current_user(), $_SERVER['REMOTE_ADDR']);

    $view = wp3_get_views_db($data_id);
    $view = $view + 1;
    update_user_meta(profile_get_current_user(), '__profile_views', apply_filters('wp3_insert_profile_views', $view));
    
    do_action('wp3_insert_profile_views', profile_get_current_user(), $view);
}

function wp3_get_profile_views($user_id = false) {  
    if (!$user_id) $user_id = get_current_user_id();
    
    $views = get_user_meta($user_id, '__profile_views', true);    
    $views = empty($views) ? 0 : $views;
    
    return apply_filters('wp3_get_profile_views', $views);
}

function wp3_get_views_db($user_id) {
    return wp3_meta_total_count('profile_view', $user_id);
}

function wp3_is_already_viewed($user_id, $data_id, $type = 'question') {

    $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
    
    $done = wp3_meta_user_done('profile_view', $user_id, $data_id, $ip);
    
    return $done > 0 ? true : false;
}

