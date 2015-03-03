<?php
/**
     * Profile fields class
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

class Profile_Custom_Fields
{
    public $user_fields;

    public function __construct()
    {

        add_action('show_user_profile', array($this, 'custom_fields'));
        add_action('edit_user_profile', array($this, 'custom_fields'));

        add_action('personal_options_update', array($this, 'save_custom_fields'));
        add_action('edit_user_profile_update', array($this, 'save_custom_fields'));
    }


    public function custom_fields($user)
    { 
        profile_all_fields_form($user->ID);
    }

    public function save_custom_fields($user_id)
    {
        $submitted_fields = profile_get_profile_fields_post_form();

        if (!profile_have_fields() || $submitted_fields === false) {
            return;
        }

        $fields = profile_get_all_fields();

        foreach ($fields as $f) {

            if (isset($submitted_fields['__profile_field_'.$f->ID])) {
                profile_update_user_field($user_id, $f->ID, $submitted_fields['__profile_field_'.$f->ID]);
            }
        }

    }

}