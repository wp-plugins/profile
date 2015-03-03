<?php
/**
     * User custom fields
     *
     * @package  	Profile
     * @author      Rahul Aryan <rah12@live.com>
     * @license  	http://www.opensource.org/licenses/gpl-license.php GPL v3.0 (or later)
     * @link     	http://wp3.in
     */


/**
 * Default profile field types
 * @return array
 */
function profile_field_types()
{
    $default_types = array(
        'text'             => __('Text', 'profile'),        
        'select'           => __('Select', 'profile'),
        'checkbox'         => __('Checkbox', 'profile'),
        'radio'            => __('Radio', 'profile'),
        'textarea'         => __('Textarea', 'profile'),
        'upload'           => __('Upload', 'profile'),
        'editor'           => __('Editor', 'profile'),
        'text_url'         => __('URL', 'profile'),
        );

    /** 
     * FILTER: profile_field_types
     * Can be used to override the default field types.
     */
    return apply_filters('profile_field_types', $default_types);
}

/**
 * Default profile visibility
 * @return array
 */
function profile_visibilities()
{
    $default_visibilities = array(
        'public'            => __('Public', 'profile'),        
        'me'                => __('Only Me', 'profile'),        
        'registered'        => __('Registered', 'profile'),
        'admin'             => __('Administrator', 'profile'),
        );

    /** 
     * FILTER: profile_field_types
     * Can be used to override the default field types.
     */
    return apply_filters('profile_visibilities', $default_visibilities);
}

function profile_field_locations()
{
    $default = array(
        'card_about' => __('User card - about'),
        'card_links' => __('User card - links')
        );

    /** 
     * FILTER: profile_field_locations
     * Can be used to override the default field locations.
     */
    return apply_filters('profile_field_locations', $default);
}


/**
 * Return all options of a field
 * @param  int $field_id post ID
 * @return stdClass
 */
function profile_get_field_options($field_id)
{
    $metas = get_post_meta($field_id);
    $options = new stdClass;

    if (is_array($metas))
        foreach ($metas as $k => $meta)
            if (strpos($k, '__field_') !== false) {
                if (count($meta) > 1)
                    $options->$k = maybe_unserialize($meta);
                else
                    $options->$k = maybe_unserialize($meta[0]);
            }

            return $options;
        }

/**
 * Extracts custom profile fields from user form
 * @return integer | boolean
 */
function profile_get_profile_fields_post_form() {

    
    $fields = array();
    
    foreach ($_POST as $k => $f) {
        if (strpos($k, '__profile_field_') !== false)
            $fields[$k] = $f;
    }

    return $fields;
}

/**
 * Get field by id
 * @param  integer $field_id post id
 * @return object
 */
function profile_get_field($field_id)
{
    $field = get_post($field_id);

    if ($field->post_type != 'profile_field') {
        return false;
    }

    $options = profile_get_field_options($field_id);

    return (object) array_merge((array) $field, (array) $options);
}

/**
 * Get fields by group specified
 * @param  boolean | string $group_name Term slug or false for non grouped
 * @return array
 */
function profile_get_fields_by_group($group_name = false)
{
    $args = array(
        'post_type' => 'profile_field',
        'orderby' => 'ID',
        'tax_query' => array(
            array('taxonomy' => 'profile_group'),
            )
        );

    if (!$group_name) {
        $args['tax_query'][0]['operator'] = 'NOT EXISTS';
    } else {
        $args['tax_query'][0]['field'] = 'slug';
        $args['tax_query'][0]['terms'] = $group_name;
    }

    return get_posts($args);
}

/**
 * Return all fields
 * @return array
 */
function profile_get_all_fields()
{
    return get_posts(array('post_type' => 'profile_field', 'orderby' => 'ID'));
}


/**
 * @param string $location
 */
function profile_fields_by_location($location)
{
    return get_posts(array('post_type' => 'profile_field', 'orderby' => 'meta_value ID', 'meta_key' => '__profile_field_show', 'meta_value' => $location));
}

/**
 * Output form of a field type
 * @param  object $field
 * @return void
 */
function profile_field_type_form($field, $user_id)
{
    $options = profile_get_field_options($field->ID);
    $html = profile_get_theme_location('field_types/form/'.$options->__field_type.'.php');
    
    echo '<div class="profile-field-type-form clearfix">';

    if (file_exists($html)) {
        include $html;
    } else {
        printf(__('HTML template for %1$s does not exists. Create a file called field_types/form/%1$s.php in your active theme directory.', 'profile'), $options->__field_type);
    }

    echo '</div>';
}

/**
 * Output view of a field type
 * @param  object $field
 * @return void
 */
function profile_field_type_view($field, $user_id)
{
    $options = profile_get_field_options($field->ID);
    $html = profile_get_theme_location('field_types/view/'.$options->__field_type.'.php');
    
    echo '<div class="profile-field-type-view clearfix">';

    if (file_exists($html)) {
        include $html;
    } else {
        printf(__('HTML template for %1$s does not exists. Create a file called field_types/view/%1$s.php in your active theme directory.', 'profile'), $options->__field_type);
    }

    echo '</div>';
}

function profile_all_fields_form()
{
    $field_groups = get_terms('profile_group', array('hide_empty' => true));

    // for ungroup fields
    $field_groups[] = false;

    foreach ($field_groups as $group) {
        
        if (!$group) {
            echo '<h3>'.__('Non grouped', 'profile').'</h3>';
        } else {
            echo '<h3>'.$group->name.'</h3>';
        }

        $fields = profile_get_fields_by_group($group->slug);

        if ($fields) {
            foreach ($fields as $field) {
                $options = profile_get_field_options($field->ID);
            }
            profile_field_type_form($field);
        }
    }
}

/**
 * Sanitize and update profile field of user
 * @param  int          $user_id
 * @param  int          $field_id
 * @return boolean
 */
function profile_update_user_field($user_id, $field_id, $value)
{
    $field = profile_get_field($field_id);

    if ('text' == $field->__field_type || 'checkbox' == $field->__field_type || 'radio' == $field->__field_type || 'select' == $field->__field_type) 
    {
        $value = sanitize_text_field($value);
        return update_user_meta($user_id, '__profile_field_'.$field->ID, $value);
    } elseif ('textarea' == $field->__field_type) 
    {
        $value = esc_textarea($value);
        return update_user_meta($user_id, '__profile_field_'.$field->ID, $value);
    } elseif ('text_url' == $field->__field_type) 
    {
        $value = esc_url($value);
        return update_user_meta($user_id, '__profile_field_'.$field->ID, $value);
    } elseif ('editor' == $field->__field_type) 
    {
        $value = wp_kses($value, profile_allowed_editor_tags());
        return update_user_meta($user_id, '__profile_field_'.$field->ID, $value);
    }


    /**
     * FILTER: profile_update_user_field
     * Action to when saving a profile field
     */
    do_action('profile_update_user_field', $user_id, $field_id, $value);
}
