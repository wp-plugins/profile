<?php

/**
 * Profile theme class
 *
 * @link http://wp3.in
 * @since 0.0.1
 * @package Profile
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Profile_Theme
{
    public function __construct() {
        add_shortcode('profile', array(Profile_BasePage_Shortcode::get_instance(), 'profile_sc'));
        profile_register_user_page('profile', __('Profile', 'profile'), array($this, 'profile_page'));
        profile_register_user_page('about', __('About', 'profile'), array($this, 'profile_about'));
        
        profile_register_user_page('favorites', __('Favorites', 'profile'), array($this, 'page_favorites'));
        profile_register_user_page('posts', __('Posts', 'profile'), array($this, 'page_my_posts'));

        if(defined('AP_VERSION')){
            profile_register_user_page('questions', __('Questions', 'profile'), array($this, 'page_questions'));
            profile_register_user_page('answers', __('Answers', 'profile'), array($this, 'page_answers'));
        }

        
        add_action('init', array($this, 'theme_function') );
        add_filter('wp_title', array($this, 'wp_title'), 100, 2);
        add_filter('the_title', array($this, 'the_title'), 100, 2);
        
        add_filter('profile_blocks', array($this, 'users_qa'));
        add_filter('profile_blocks', array($this, 'user_posts'));
        add_filter('ap_post_actions_buttons', array($this, 'post_action_favorite_button'));
    }
    
    public function profile_page() {
        include (profile_get_theme_location('profile.php'));
    }
    
    public function profile_about() {
        include (profile_get_theme_location('about.php'));
    }
    
    public function page_my_posts() {
        include (profile_get_theme_location('posts.php'));
    }
    
    public function page_favorites() {
        include (profile_get_theme_location('favorites.php'));
    }

    public function theme_function()
    {
        require_once profile_get_theme_location('functions.php');
    }

    public function page_questions()
    {
        require_once profile_get_theme_location('questions.php');
    }

    public function page_answers()
    {
        require_once profile_get_theme_location('answers.php');
    }
    
    /**
     * @param string $title
     * @return void
     */
    public function wp_title($title) {
        if (is_profile()) {
            $new_title = profile_page_title();
            
            $new_title = str_replace('PROFILE_TITLE', $new_title, $title);
            $new_title = apply_filters('ap_title', $new_title);
            
            return $new_title;
        }
        
        return $title;
    }
    
    public function the_title($title, $id) {

        if ($id == profile_opt('base_page')) {
            return profile_page_title();
        }
        return $title;
    }
    
    public function users_qa($user_id) {
        if(!defined('AP_VERSION'))
            return;

        $args = array('post_type' => array('question', 'answer'), 'author' => profile_get_current_user(), 'showposts' => 5);
        
        $posts = new WP_Query($args);
        
        if ($posts->have_posts()) {
            ?>
            <h3 class="profile-list-head">
                <?php
                _e('Questions & Answers', 'profile') ?>
                <span class="user-post-count">(<?php
                    echo count_user_posts(profile_get_current_user(), 'question') + count_user_posts(profile_get_current_user(), 'answer') ?>)</span>
                </h3>
                <?php
                while ($posts->have_posts()):
                    $posts->the_post();
                include profile_get_theme_location('content-question.php');
                endwhile;
                echo '<div class="show-all">' . __('Show all', 'profile') . ' <a href="' . profile_get_link_to('answers') . '">' . __('answers by', 'profile') . '</a> ' . profile_display_name(profile_get_current_user()) . ' &rarr; <span>  |  </span>' . __('Show all', 'profile') . ' <a href="' . profile_get_link_to('questions') . '">' . __('questions by', 'profile') . '</a> ' . profile_display_name(profile_get_current_user()) . ' &rarr;</div>';
            } 
            else {
                _e('No question and answer posted by this user.', 'profile');
            }
            wp_reset_postdata();
    }

    public function user_posts($user_id) {
        $args = array('post_type' => 'post', 'author' => profile_get_current_user(), 'showposts' => 5);
        
        $posts = new WP_Query($args);
        
        if ($posts->have_posts()) {
            ?>
            <h3 class="profile-list-head">
                <?php
                _e('Posts', 'profile') ?>
                <span class="user-post-count">(<?php
                    echo count_user_posts(profile_get_current_user(), 'post') ?>)</span>
                </h3>
                <?php
                while ($posts->have_posts()):
                    $posts->the_post();
                include profile_get_theme_location('content-post.php');
                endwhile;
                echo '<div class="show-all">' . __('Show all', 'profile') . ' <a href="' . profile_get_link_to('my-posts') . '">' . __('posts by', 'profile') . '</a> ' . profile_display_name(profile_get_current_user()) . ' &rarr;</div>';
            } 
            else {
                _e('No posts were written by this user.', 'profile');
            }
            wp_reset_postdata();
    }

    public function post_action_favorite_button($metas)
    {
        $metas[] = wp3_favorite_btn(get_the_ID());

        return $metas;
    }
}
