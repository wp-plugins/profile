<?php
/**
     * Profile process form
     * @link http://wp3.in
     * @since 0.0.1
     * @license GPL 3+
     * @package Profile
     */

class Profile_Process_Form
{
    private $fields;

    private $result;

    private $redirect;

    private $is_ajax = false;
    /**
     * Initialize the class
     */
    public function __construct()
    {

        add_action('init', array($this, 'non_ajax_form'));
        add_action('wp_ajax_profile_ajax', array($this, 'profile_ajax'));
        add_action('wp_ajax_nopriv_profile_ajax', array($this, 'profile_ajax'));
    }

    /**
     * for non ajax form
     * @return void
     */
    public function non_ajax_form()
    {
        //return if profile_form_action is not set, probably its not our form
        if (!isset($_REQUEST['profile_form_action']) || isset($_REQUEST['profile_ajax_action']))
            return;

        $this->request = $_REQUEST;
        $this->process_form();

        if (!empty($this->redirect)) {
            wp_redirect($this->redirect);
            exit;
        }
    }

    /**
     * Handle all anspress ajax requests 
     * @return void
     * @since 2.0.1
     */
    public function profile_ajax()
    {

        if (!isset($_REQUEST['profile_ajax_action']))
            return;

        $this->request = $_REQUEST;

        if (isset($_POST['profile_form_action'])) {
            $this->is_ajax = true;
            $this->process_form();
            profile_send_json($this->result);
        } else {
            $action = sanitize_text_field($this->request['profile_ajax_action']);

            /**
             * ACTION: profile_ajax_[$action]
             * Action for processing Ajax requests
             * @since 2.0.1
             */
            do_action('profile_ajax_'.$action);
        }
    }


    /**
     * Process form based on action value
     * @return void
     * @since 2.0.1
     */
    public function process_form()
    {

        $action = sanitize_text_field($_POST['profile_form_action']);

        switch ($action) {
            case 'update_user_meta_field':
            $this->update_user_meta_field();
            break;

            default:
                /**
                 * ACTION: profile_process_form_[action]
                 * process form
                 * @since 2.0.1
                 */
                do_action('profile_process_form_'.$action);
                break;
            }

        }



    /**
     * Process user meta field
     * @return void
     * @since 2.0.1
     */
    public function update_user_meta_field()
    {
        $field_name = sanitize_text_field($_POST['field']);

        if (!isset($_POST['field']) || !isset($_POST['__nonce']) || !is_user_logged_in() || profile_get_current_user() != get_current_user_id()) {
            $this->result = array('message' => 'something_wrong');
            return;
        }

        $nonce_action = 'field_'.$field_name.'_'.get_current_user_id();

        if (!wp_verify_nonce($_POST['__nonce'], $nonce_action)) {
            $this->result = array('message' => 'something_wrong');
            return;
        }

        



        /*$args['first_name']['sanitize'] = array('sanitize_text_field');
        $args['last_name']['sanitize'] = array('sanitize_text_field');
        $args['nickname']['sanitize'] = array('sanitize_text_field');
        $args['url']['sanitize'] = array('url');
        $args['display_name']['sanitize'] = array('sanitize_text_field');*/


        if ($field_name == 'display_name')
        {
            wp_update_user(array('ID' => get_current_user_id(), 'display_name' => sanitize_text_field($_POST['__profile_field_display_name'])));
            $profile_user_obj = get_user_by('id', get_current_user_id());
            $display_name = $profile_user_obj->data->display_name;

            ob_start();
            ?>
            <div data-cont="field_display_name" class="meta-field">
                <span class="meta-fields-label"><?php _e('Display name', 'profile') ?></span>
                <div class="meta-values">				
                   <?php if (profile_user_can_edit_field(get_current_user_id())): ?>
                      <a class="btn-edit-profile-field" href="#" data-action="edit_profile_field" data-query="field=display_name&profile_ajax_action=edit_profile_field"><?php _e('Edit', 'profile') ?></a>
                  <?php endif; ?>

                  <div class="user-field-form">
                      <span class="meta-field-value"><?php echo $display_name ?></span>
                  </div>
              </div>
          </div>
          <?php
            $html = ob_get_clean();
        }
        elseif ($field_name == 'name')
        {
        if (!empty($_POST['__profile_field_first_name']))
            update_user_meta(get_current_user_id(), 'first_name', sanitize_text_field($_POST['__profile_field_first_name']));

        if (!empty($_POST['__profile_field_last_name']))
            update_user_meta(get_current_user_id(), 'last_name', sanitize_text_field($_POST['__profile_field_last_name']));

        ob_start();
        ?>
        <div data-cont="field_display_name" class="meta-field">
            <span class="meta-fields-label"><?php _e('Name', 'profile') ?></span>
            <div class="meta-values">				
               <?php if (profile_user_can_edit_field(get_current_user_id())): ?>
                  <a class="btn-edit-profile-field" href="#" data-action="edit_profile_field" data-query="field=name&profile_ajax_action=edit_profile_field"><?php _e('Edit', 'profile') ?></a>
              <?php endif; ?>

              <div class="user-field-form">
                  <span class="meta-field-value"><?php echo get_user_meta(get_current_user_id(), 'first_name', true).' '.get_user_meta(get_current_user_id(), 'last_name', true) ?></span>
              </div>
          </div>
      </div>
      <?php
        $html = ob_get_clean();
    }
    elseif ($field_name == 'nickname')
    {
    if (!empty($_POST['__profile_field_nickname']))
        update_user_meta(get_current_user_id(), 'nickname', sanitize_text_field($_POST['__profile_field_nickname']));

    ob_start();
    ?>
    <div data-cont="field_nickname" class="meta-field">
        <span class="meta-fields-label"><?php _e('Nickname', 'profile') ?></span>
        <div class="meta-values">				
           <?php if (profile_user_can_edit_field(get_current_user_id())): ?>
              <a class="btn-edit-profile-field" href="#" data-action="edit_profile_field" data-query="field=nickname&profile_ajax_action=edit_profile_field"><?php _e('Edit', 'profile') ?></a>
          <?php endif; ?>

          <div class="user-field-form">
              <span class="meta-field-value"><?php echo get_user_meta(get_current_user_id(), 'nickname', true) ?></span>
          </div>
      </div>
  </div>
  <?php
    $html = ob_get_clean();
}
elseif ($field_name == 'description')
{
    if (!empty($_POST['__profile_field_description']))
        update_user_meta(get_current_user_id(), 'description', sanitize_text_field($_POST['__profile_field_description']));

    ob_start();
    ?>
    <div data-cont="field_description" class="meta-field">
        <span class="meta-fields-label"><?php _e('About me', 'profile') ?></span>
        <div class="meta-values">				
           <?php if (profile_user_can_edit_field(get_current_user_id())): ?>
              <a class="btn-edit-profile-field" href="#" data-action="edit_profile_field" data-query="field=description&profile_ajax_action=edit_profile_field"><?php _e('Edit', 'profile') ?></a>
          <?php endif; ?>

          <div class="user-field-form">
              <span class="meta-field-value"><?php echo get_user_meta(get_current_user_id(), 'description', true) ?></span>
          </div>
      </div>
  </div>
  <?php
    $html = ob_get_clean();
} else
{
    $field = profile_get_field($field_name);

    if (!$field) {
        $this->result = array('message' => 'something_wrong');
        return;
    }
    profile_update_user_field(get_current_user_id(), $field->ID, $_POST['__profile_field_'.$field->ID]);

    ob_start();
    profile_field_type_view($field, get_current_user_id());
    $html = ob_get_clean();
}


$this->result = array(
    'action' 		=> 'updated_user_meta_field',
    'message'		=> 'updated_user_field',
    'container'		=> '[data-cont="field_'.$field_name.'"]',
    'do'			=> 'replace',
    'html'			=> $html,
    );

}

}
