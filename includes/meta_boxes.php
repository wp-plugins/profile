<?php
/**
     * Profile cpt meta boxes
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

class Profile_Meta_Boxes
{

    /**
     * Initialize the class
     */
    public function __construct()
    {
        add_action('load-post.php', array($this, 'meta_box_setup'));
        add_action('load-post-new.php', array($this, 'meta_box_setup'));
        add_action('save_post', array($this, 'meta_box_save'), 10, 2);
        
    }

    public function meta_box_setup()
    {
        add_meta_box('profile_field_option', esc_html__('Field options', 'profile'), array($this, 'field_options'), 'profile_field', 'normal', 'high');
        add_meta_box('profile_field_type_option', esc_html__('Field type options', 'profile'), array($this, 'field_type_options'), 'profile_field', 'normal', 'high');
        add_meta_box('profile_field_show', esc_html__('Where to show ?', 'profile'), array($this, 'profile_field_show'), 'profile_field', 'normal', 'high');
    }

    public function field_options($object, $box)
    {
        ?>

            <?php wp_nonce_field(basename(__FILE__), 'profile_field_option_nonce'); ?>

            <div class="profile_field_meta_boxes">
                <div class="profile_field_meta_box required clearfix">
                    <div class="profile_field_label">
                        <label for="field_label"><?php _e('Label', 'profile'); ?></label>
                        <p class="profile_field_desc"><?php _e('Label of the field', 'profile') ?></p>
                    </div>
                    <div class="profile_field_input">
                        <input class="widefat" type="text" name="field_label" id="field_label" value="<?php echo esc_attr($object->post_title); ?>" size="30" placeholder="<?php _e('i.e. Date of birth', 'profile') ?>" />                        
                    </div>
                </div>
                <div class="profile_field_meta_box clearfix">
                    <div class="profile_field_label">
                        <label for="field_visibility"><?php _e("Visibility", 'profile'); ?></label>
                        <p class="profile_field_desc"><?php _e('Select the visibility of field.', 'profile') ?></p>
                    </div>
                    <div class="profile_field_input">
                        <select type="text" name="field_visibility" id="field_visibility">  
                            <?php 
                                foreach (profile_visibilities() as $k => $type) {
                                                                    echo '<option value="'.$k.'" '.selected(get_post_meta($object->ID, '__field_visibility', true), $k, false).'>'.$type.'</option>';
                                }
                            ?>
                        </select>                       
                    </div>
                </div>
                
                <div class="profile_field_meta_box clearfix">
                    <div class="profile_field_label">
                        <label for="field_hide_in_admin"><?php _e('Hide in wp-admin ?', 'profile'); ?></label>
                    </div>
                    <div class="profile_field_input">
                        <input class="widefat" type="checkbox" name="field_hide_in_admin" id="field_hide_in_admin" value="<?php echo esc_attr(get_post_meta($object->ID, '__field_hide_in_admin', true)); ?>" size="30" />                        
                    </div>
                </div>
                <div class="profile_field_meta_box clearfix">
                    <div class="profile_field_label required">
                        <label for="field_type"><?php _e('Type', 'profile'); ?></label>
                        <p class="profile_field_desc"><?php _e('Select type of field', 'profile') ?></p>
                    </div>
                    <div class="profile_field_input">
                        <select type="text" name="field_type" id="field_type" data-post_id="<?php echo $object->ID ?>">  
                            <?php 
                                foreach (profile_field_types() as $k => $type) {
                                                                    echo '<option value="'.$k.'" '.selected(get_post_meta($object->ID, '__field_type', true), $k, false).'>'.$type.'</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        <?php
    }

    public function field_type_options($object, $box)
    {
        echo '<div id="profile_field_type_fields" class="profile_field_meta_boxes">';
        echo '</div>';
    }

    public function profile_field_show($object, $box)
    {
        $value = get_post_meta($object->ID, '__profile_field_show');

        ?>
            <div class="profile_field_meta_boxes">
                <div class="profile_field_meta_box clearfix">
                    <div class="profile_field_label">
                        <label for="profile_field_show"><?php _e('Show in', 'profile'); ?></label>
                        <p class="profile_field_show"><?php _e('Check the locations', 'profile') ?></p>
                    </div>
                    <div class="profile_field_input">
                        <?php foreach (profile_field_locations() as $k => $name) { ?>
                            <label>
                                <input type="checkbox" name="profile_field_show[<?php echo $k ?>]" id="profile_field_show" value="1" <?php checked(in_array($k, @$value), true) ?> />
                                <?php echo $name ?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php
    }

    public function meta_box_save($post_id, $post)
    {

        if (!isset($_POST['profile_field_option_nonce']) || !wp_verify_nonce($_POST['profile_field_option_nonce'], basename(__FILE__))) {
                    return $post_id;
        }

        if ($post->post_type != 'profile_field') {
                    return $post_id;
        }

        if (!current_user_can('manage_options')) {
                    return $post_id;
        }
        
        // unhook this function so it doesn't loop infinitely
        remove_action('save_post', array($this, 'meta_box_save'));

        $field_label = sanitize_text_field($_POST['field_label']);

        wp_update_post(array('ID'=> $post_id, 'post_title' => $field_label));
        
        $meta_fields = array('field_visibility', 'field_hide_in_admin', 'field_type', 'field_is_required', 'field_default_value', 'field_placeholder', 'field_options', 'field_input_class', 'profile_field_show');
        
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        foreach ($meta_fields as $fields) {
            
            if (!empty($_POST[$fields]) && is_array($_POST[$fields])) {
                $field_post = $_POST[$fields];
                $new_field_post = array();
                foreach ($field_post as $key => $value) {
                    if (!empty($value))
                        $new_field_post[$key] = $value;
                }
                $field_post = $new_field_post;
            }
            else
                $field_post = sanitize_text_field($_POST[$fields]);

            if ($fields == 'profile_field_show') {
                foreach (profile_field_locations() as $k => $name) {
                    if (isset($field_post[$k]))
                        add_post_meta($post_id, '__'.$fields, $k);
                    else
                        delete_post_meta($post_id, '__'.$fields, $k);
                }
            }
            elseif (!empty($field_post))
            {
                update_post_meta($post_id, '__'.$fields, $field_post);
            }
        }

        // unhook this function so it doesn't loop infinitely
        add_action('save_post', array($this, 'meta_box_save'));
    }
   
}
