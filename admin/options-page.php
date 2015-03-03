<?php
class Profile_Options_Page
{
    static public function add_option_groups() {
        $settings = profile_opt();
        
        // Register general settings
        profile_register_option_group('general', __('General', 'profile') , array(
            array(
                'name'           => 'profile_opt[base_page]',
                'label'           => __('Base page', 'profile') ,
                'description'           => __('Select page for displaying anspress.', 'profile') ,
                'type'           => 'page_select',
                'value'           => @$settings['base_page'],
            ) ,
            array(
                'name'           => 'profile_opt[author_credits]',
                'label'           => __('Hide author credits', 'profile') ,
                'description'           => __('Show your love by showing link to AnsPress project site.', 'profile') ,
                'type'           => 'checkbox',
                'value'           => @$settings['author_credits'],
                'order'           => '1',
            ) ,
            
            array(
                'name'           => 'profile_opt[allow_private_posts]',
                'label'           => __('Allow private posts', 'profile') ,
                'description'           => __('Allow users to create private question and answer.', 'profile') ,
                'type'           => 'checkbox',
                'value'           => @$settings['allow_private_posts'],
            ) ,
        ));
        
        $cpt_group = array();
        
        foreach (get_post_types(array('public' => true )) as $post_type) {
            if ($post_type != 'attachment' && $post_type != 'revision' && $post_type != 'nav_menu_item') {
                $cpt_group[$post_type]['label']           = $post_type;
                $cpt_group[$post_type]['name']           = 'profile_opt[favorite_cpt][' . $post_type . ']';
                $cpt_group[$post_type]['value']           = $settings['favorite_cpt'][$post_type];
            }
        }
        
        // Favorite
        profile_register_option_group('favorite', __('Favorite', 'profile') , array(
            
            array(
                'name' => 'profile_opt[favorite_cpt]',
                'label' => __('Post type user can favorite', 'profile') ,
                'description' => __('Check the cpt user can add to their favorite list.', 'profile') ,
                'type' => 'checkbox',
                'value' => @$settings['favorite_cpt'],
                'group' => $cpt_group,
            )
        ));
    }
}
