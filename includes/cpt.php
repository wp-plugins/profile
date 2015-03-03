<?php
/**
     * Profile fields CPT
     *
     * @package   Profile
     * @author    Rahul Aryan <admin@rahularyan.com>
     * @license   GPL-2.0+
     * @link      http://rahularyan.com
     * @copyright 2014 Rahul Aryan
     */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Profile_CPT
{

    /**
     * Initialize the class
     */
    public function __construct()
    {
        //Register Custom Post types and taxonomy
        add_action('init', array($this, 'register_cpt'), 0);
        add_action('init', array($this, 'register_taxonomy'), 0);
        add_action('profile_group_edit_form_fields', array($this, 'profile_group_edit_fields'));
        add_action('profile_group_add_form_fields', array($this, 'profile_group_fields'));
        
        // save extra category extra fields hook
        add_action('edited_profile_group', array($this, 'save_profile_group'));
        add_action('created_profile_group', array($this, 'save_profile_group'));
        add_filter('manage_edit-profile_group_columns', array($this, 'add_profile_group_columns'));
        add_filter('manage_profile_group_custom_column', array($this, 'add_profile_group_column_content'), 10, 3);

    }

    /**
     * Register Profile fields CPT
     * @return void
     * @since 0.0.1
     */
    public function register_cpt() {
        
        // question CPT labels
        $labels = array(
            'name'              => _x('Profile fields', 'Post Type General Name', 'profile'),
            'singular_name'     => _x('Profile field', 'Post Type Singular Name', 'profile'),
            'menu_name'         => __('Profile fields', 'profile'),
            'parent_item_colon' => __('Parent profile field:', 'profile'),
            'all_items'         => __('All profile fields', 'profile'),
            'view_item'         => __('View profile field', 'profile'),
            'add_new_item'      => __('Add new profile field', 'profile'),
            'add_new'           => __('New profile field', 'profile'),
            'edit_item'         => __('Edit profile field', 'profile'),
            'update_item'       => __('Update profile field', 'profile'),
            'search_items'      => __('Search profile field', 'profile'),
            'not_found'         => __('No profile field found', 'profile'),
            'not_found_in_trash' => __('No profile fields found in trash', 'profile')
        );
        
        /**
         * FILTER: profile_profile_field_cpt_labels
         * filter is called before registering profile_field CPT
         */
        $labels = apply_filters('profile_profile_field_cpt_labels', $labels);

        // question CPT arguments
        $args   = array(
            'label' => __('Profile fields', 'profile'),
            'description' => __('Fields for profile', 'profile'),
            'labels' => $labels,
            'supports' => array(''),
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => true,
            'menu_icon' => 'profileicon-profile_logo',
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'post',
            'rewrite' => false,
            'query_var' => 'profile_field',
        );

        /**
         * FILTER: profile_profile_field_cpt_args 
         * filter is called before registering profile_field CPT
         */
        $args = apply_filters('profile_profile_field_cpt_args', $args);

        // register CPT question
        register_post_type('profile_field', $args);
    }

    public function register_taxonomy() {
        $labels = array(
            'name'              => _x('Profile field group', 'taxonomy general name', 'profile'),
            'singular_name'     => _x('Profile field group', 'taxonomy singular name', 'profile'),
            'search_items'      => __('Search profile field group', 'profile'),
            'all_items'         => __('All profile field group', 'profile'),
            'parent_item'       => __('Parent profile field group', 'profile'),
            'parent_item_colon' => __('Parent profile field group:', 'profile'),
            'edit_item'         => __('Edit profile field group', 'profile'),
            'update_item'       => __('Update profile field group', 'profile'),
            'add_new_item'      => __('Add new profile field group', 'profile'),
            'new_item_name'     => __('New profile field group', 'profile'),
            'menu_name'         => __('Profile field group', 'profile'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => 'profile_group',
            'rewrite'           => false,
        );

        register_taxonomy('profile_group', array('profile_field'), $args);
    }

    public function profile_group_edit_fields($tax) {
        $t_id = $tax->term_id;
        $tax_meta = get_option("profile_group_$t_id");
    ?>
            
        <tr class="form-field ap-label-visibility-field">
            <th>
                <label for="profile_group_field"><?php _e('Visibility', 'profile'); ?></label>
            </th>
            <td>
                <select type="text" name="profile_group[visibility]" id="profile_group_field">  
                        <?php 
                            foreach (profile_visibilities() as $k => $type) {
                                                            echo '<option value="'.$k.'" '.selected($tax_meta['visibility'], $k, false).'>'.$type.'</option>';
                            }
                        ?>
                </select> 
                <br />
            </td>
        </tr>
            
        
    <?php
    }

    public function profile_group_fields($tax) {
        $t_id = $tax->term_id;
        $tax_meta = get_option("profile_group_$t_id");

    ?>
            
        <tr class="form-field ap-label-visibility-field">
            <th>
                <label for="profile_group_field"><?php _e('Visibility', 'profile'); ?></label>
            </th>
            <td>
                <select type="text" name="profile_group[visibility]" id="profile_group_field">  
                        <?php 
                            foreach (profile_visibilities() as $k => $type) {
                                                            echo '<option value="'.$k.'" '.selected($tax_meta['visibility'], $k, false).'>'.$type.'</option>';
                            }
                        ?>
                </select> 
                <br />
                <br />
            </td>
        </tr>           
        
    <?php
    }

    // save extra category extra fields callback function
    public function save_profile_group($term_id) {
        if (isset($_POST['profile_group'])) {
            $t_id = $term_id;
            $tax_meta = get_option("profile_group_$t_id");
            $tax_keys = array_keys($_POST['profile_group']);
                foreach ($tax_keys as $key) {
                if (isset($_POST['profile_group'][$key])) {
                    $tax_meta[$key] = sanitize_text_field($_POST['profile_group'][$key]);
                }
            }
            //save the option array
            update_option("profile_group_$t_id", $tax_meta);
        }
    }

    function add_profile_group_columns($columns) {
        $columns['visibility'] = __('Visibility', 'profile');
        return $columns;
    }
    
     
    function add_profile_group_column_content($content, $column_name, $term_id) {
        $visibility = profile_get_label_visibility($term_id);
        $content .= $visibility;
        return $content;
    }

   
}
