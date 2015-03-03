<?php

/**
 * Common theme and template functions
 *
 * @link http://wp3.in
 * @since 0.0.1
 * @package Profile
 */

/**
 * Output profile navigation
 * Extract menu from registered user pages
 * @return void
 * @since 2.0.1
 */
function profile_navigation() {
    global $user_pages, $profile_navigation;
    
    $userid             = profile_get_current_user();
    $user_page          = get_query_var('profile_page');
    $user_page          = $user_page ? $user_page : 'profile';
    
    $menus              = array();
    
    foreach ($user_pages as $k                  => $args) {
        $link               = profile_get_link_to($k, $userid);
        $class              = $user_page == $k ? 'active' : '';
        $menus[$k]                    = array(
            'title'                    => $args['title'],
            'link'                    => $link,
            'order'                    => 10,
            'class'                    => $class
            );
    }
    
    /**
     * FILTER: profile_navigation
     * filter is applied before showing user menu
     * @var array
     * @since  0.0.1
     */
    $profile_navigation = apply_filters('profile_navigation', $menus);
    
    $profile_navigation = wp3_sort_array_by_order($profile_navigation);
    
    include (profile_get_theme_location('navigation.php'));
}

/**
 * Output current profile page
 * @return void
 * @since 2.0.0-beta
 */
function profile_page() {
    global $user_pages, $wp;

    $current_page = get_query_var('profile_page');
    
    if ($current_page == '') {
        $current_page = 'profile';
    }
    
    if (profile_get_current_user() == 0) {
        echo '<div class="login-message">' . sprintf(__('Please %slogin%s to see access your profile', 'profile') , '<a href="' . wp_login_url(profile_get_link_to('profile')) . '">', '</a>') . '</div>';
        return;
    }
    
    if (isset($user_pages[$current_page]['func'])) {
        call_user_func($user_pages[$current_page]['func']);
    } 
    else {
        include (profile_get_theme_location('404.php'));
    }
}

function profile_about_me() {
    return profile_current_user_meta('description', true);
}

function profile_user_name() {
    $profile_user_obj = profile_current_user_object();
    
    $user_name = $profile_user_obj->data->user_login;
    
    return apply_filters('profile_user_name', $user_name);
}

function profile_display_all_metas() {
    $user_id          = profile_get_current_user();
    $profile_user_obj = profile_current_user_object();
    
    $fields           = profile_get_all_fields();
    
    $default_fields   = array();
    
    $default_fields['display_name']                  = array(
        'label'                  => __('Disply name', 'profile') ,
        'value'                  => $profile_user_obj->data->display_name
        );
    $default_fields['name']                  = array(
        'label'                  => __('Name', 'profile') ,
        'value'                  => profile_current_user_meta('first_name', true) . ' ' . profile_current_user_meta('last_name', true)
        );
    $default_fields['nickname']                  = array(
        'label'                  => __('Nick name', 'profile') ,
        'value'                  => profile_current_user_meta('nickname', true)
        );
    $default_fields['description']                  = array(
        'label'                  => __('Description', 'profile') ,
        'value'                  => profile_current_user_meta('description', true)
        );
    
    $field_groups     = get_terms('profile_group', array(
        'hide_empty'                  => true
        ));
    
    // for ungroup fields
    $field_groups[]                  = false;
    
    echo '<h3 class="meta-group-title">' . __('Basic', 'profile') . '</h3>';
    echo '<div id="meta_group_basic" class="meta-group"><div class="meta-group-fields">';
    foreach ($default_fields as $k => $value) {
        if (!empty($value) || is_my_profile()) {
            ?>  
            <div data-cont="field_<?php
            echo $k
            ?>" class="meta-field">
            <span class="meta-fields-label"><?php
                echo $value['label'] ?></span>
                <div class="meta-values">

                 <?php
                 if (profile_user_can_edit_field($user_id)): ?>
                 <a class="btn-edit-profile-field" href="#" data-action="edit_profile_field" data-query="field=<?php
                 echo $k
                 ?>&profile_ajax_action=edit_profile_field"><?php
                 _e('Edit', 'profile') ?></a>
                 <?php
                 endif; ?>

                 <div class="user-field-form">
                  <span class="meta-field-value"><?php
                    echo $value['value'] ?></span>
                </div>
            </div>
        </div>
        <?php
    }
}
echo '</div></div>';

foreach ($field_groups as $group) {
    if (profile_user_can_see_group($group->term_id)) {
        $fields = profile_get_fields_by_group($group->slug);

        if (!$fields) {
            return;
        }

        echo '<div id="meta_group_' . $group->slug . '" class="meta-group">';

        if (!$group) {
            echo '<h3 class="meta-group-title">' . __('Non grouped', 'profile') . '</h3>';
        } 
        else {
            echo '<h3 class="meta-group-title">' . $group->name . '</h3>';
        }

        echo '<div class="meta-group-fields">';

        foreach ($fields as $field) {
            $options = profile_get_field_options($field->ID);
            $value   = profile_current_user_meta('__profile_field_' . $field->ID, true);

            if (!empty($value) || is_my_profile()) {
                echo '<div data-cont="field_' . $field->ID . '" class="clearfix">';

                profile_field_type_view($field, $user_id);

                echo '</div>';
            }
        }

        echo '</div></div>';
    }
}
}

/**
 * Return the current page title
 * @return string
 */
function profile_page_title() {
    global $user_pages;
    
    $userid       = profile_get_current_user();
    $user_page    = get_query_var('profile_page');
    $user_page    = $user_page != '' ? sanitize_text_field($user_page) : 'profile';
    
    $title_prefix = $userid == get_current_user_id() ? __('My ', 'profile') : profile_display_name($userid) . __('\'s ', 'profile');
    
    if (isset($user_pages[$user_page]['title'])) {
        return $title_prefix . $user_pages[$user_page]['title'];
    }
    
    return __('Page not found', 'profile');
}

/**
 * user about card
 * @return void
 */
function profile_user_about_card($user_id = false) {

    if (!$user_id) {
        $user_id = profile_get_current_user();
    }
    
    $things  = array();
    
    $things['name']         = '<h2 class="user-card-name">' . profile_display_name() . '<span class="user-name">@' . profile_user_name() . '</span></h2>';
    $things['about_me']         = '<div class="bio">' . profile_about_me() . '</div>';
    
    $fields  = profile_fields_by_location('card_about');
    
    if ($fields) {
        foreach ($fields as $field) {
            $options = profile_get_field_options($field->ID);
        }
        $value   = get_user_meta($user_id, '__profile_field_' . $field->ID, true);
        
        if ($options->__field_type == 'text_url') {
            $value   = '<a href="' . $value . '" rel="nofollow">' . $field->post_title . '</a>';
        }
        
        $things['__profile_field_' . $field->ID]         = $value;
    }
    
    /**
     * FILTER: profile_user_about_card
     * Used to filter user about card
     */
    $things  = apply_filters('profile_user_about_card', $things, $user_id);
    
    if (!empty($things) && is_array($things)) {
        foreach ($things as $name     => $display) {
            echo $display;
        }
    }
}

/**
 * user about card
 * @return void
 */
function profile_user_link_card($user_id  = false) {

    if (!$user_id) $user_id  = profile_get_current_user();
    
    $things   = array();
    
    $user_url = get_user_meta($user_id, 'url', true);
    
    if (!empty($user_url)) $things['url']          = '<a class="profile-btn btn-my-website" href="' . $user_url . '" rel="nofollow">' . __('My website', 'profile') . '</a>';
    
    $user_obj = profile_current_user_object();
    
    $things['memeber_for']          = '<span class="user-profile-hit profileicon-clock">' . sprintf(__('Memeber for %s', 'profile') , human_time_diff(strtotime($user_obj->user_registered) , current_time('timestamp'))) . '</span>';
    
    $views    = get_user_meta($user_id, '__profile_views', true);
    $things['hit']          = '<span class="user-profile-hit profileicon-eye">' . sprintf(_n('1 profile view', '%d profile views', $views, 'profile') , $views) . '</span>';
    
    $fields   = profile_fields_by_location('card_links');
    
    if ($fields) {
        foreach ($fields as $field) {
            $options  = profile_get_field_options($field->ID);
        }
        $value    = get_user_meta($user_id, '__profile_field_' . $field->ID, true);
        
        if ($options->__field_type == 'text_url') {
            $value    = '<a href="' . $value . '" rel="nofollow">' . $field->post_title . '</a>';
        }
        
        $things['__profile_field_' . $field->ID]          = $value;
    }
    
    /**
     * FILTER: profile_user_link_card
     * Used to filter user about card
     */
    $things   = apply_filters('profile_user_link_card', $things, $user_id);
    
    if (!empty($things) && is_array($things)) {
        foreach ($things as $name    => $display) {
            echo $display;
        }
    }
}

function profile_blocks($user_id = false) {
    do_action('profile_blocks', $user_id);
}

function profile_avatar_upload_form() {
    if (is_my_profile()) {
        ?>
        <form method="POST" enctype="multipart/form-data" data-action="profile_upload_form">
            <div class="profile-btn profile-upload-o">
                <span><?php
                    _e('Upload avatar', 'profile'); ?></span>
                    <input type="file" name="thumbnail" class="profile-upload-input" data-action="profile-upload-field">
                </div>
                <input type='hidden' value='<?php
                echo wp_create_nonce('profile_upload'); ?>' name='__nonce' />
                <input type="hidden" name="profile_ajax_action" id="action" value="profile_upload">
                <input type="hidden" name="action" id="action" value="profile_ajax">
            </form>
            <?php
        }
    }

/**
 * Output favorite btn HTML
 * @return string
 * @since 0.0.2
 */
function wp3_favorite_btn($post_id    = false) {
    if(is_profile())
        return;
    
    if (!$post_id) $post       = get_the_ID();
    
    $total_favs = wp3_post_favorites_count($post_id);
    $favorited  = wp3_is_user_favorited($post_id);
    
    $nonce      = wp_create_nonce('favorite_' . $post_id);
    $title      = (!$favorited) ? (__('Add to favorite', 'profile')) : (__('Remove favorite', 'profile'));
    
    return '<a id="favorite_' . $post_id . '" href="#" class="profileicon-star profile-btn favorite-btn' . ($favorited ? ' active ' : ' ') . '" data-query="profile_ajax_action=favorite&p_id=' . $post_id . '&__nonce=' . $nonce . '" data-action="favorite">' . $title . '</a>';
}

function profile_user_favorite_cpt_links() {    

    $cpts = profile_opt('favorite_cpt');
    $active = get_query_var('profile_cpt');
    
    echo '<a '.($active == '' ? ' class="active" ' : '').'href="' . profile_get_link_to(array( 'profile_page' => 'favorites' )) . '">' . __('All', 'profile') . '</a>';
    foreach ($cpts as $k => $value) echo '<a '.($active == $k ? ' class="active" ' : '').'href="' . profile_get_link_to(array( 'profile_page' => 'favorites', 'profile_cpt' => $k )) . '">' . ucfirst($k) . '</a>';
}
