<?php
/**
 * Role and permissions
 *
 * @package  	Profile
 * @author      Rahul Aryan <rah12@live.com>
 * @license  	http://www.opensource.org/licenses/gpl-license.php GPL v3.0 (or later)
 * @link     	http://wp3.in
 */

function profile_user_can_see_group($group_id)
{
    if (is_my_profile() || is_super_admin( )) {
            return true;
    }

    $visibility = profile_get_label_visibility($group_id);

    if ('public' == $visibility) {
            return true;
    }
        
    if ('me' == $visibility && is_my_profile()) {
            return true;
    }

    if ('registered' == $visibility && is_user_logged_in()) {
            return true;
    }

    return false;

}

/**
 * check if user can edit profile fields
 * @param  integer $user_id
 * @return boolean
 */
function profile_user_can_edit_field($user_id)
{
    if (is_my_profile() || is_super_admin( )) {
            return true;
    }

    return false;
}
