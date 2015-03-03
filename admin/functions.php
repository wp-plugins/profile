<?php
/**
 * Get the field option by field type
 * @return void
 */
function profile_field_option_by_type($type = 'text', $post)
{
    echo '<div id="profile_field_type_fields" class="profile_field_meta_boxes">';
    ?>

		<div class="profile_field_meta_box clearfix">
	        <div class="profile_field_label">
	            <label for="field_is_required"><?php _e('Required ?', 'profile'); ?></label>
	        </div>
	        <div class="profile_field_input">
	        	<label>
	            	<input type="checkbox" name="field_is_required" id="field_is_required" value="<?php echo esc_attr(get_post_meta($post->ID, '__field_is_required', true)); ?>" />
	            	<?php _e('Yep, I think they must fill this field', 'profile') ?>
	            </label>                        
	        </div>
	    </div>
	<?php

    if ('text' == $type || 'select' == $type || 'radio' == $type) {
        ?>
            <div class="profile_field_meta_box clearfix">
                <div class="profile_field_label">
                    <label for="field_default_value"><?php _e('Default value', 'profile'); ?></label>
                    <p class="profile_field_desc"><?php _e('Default value to be used if nothing entered by user', 'profile') ?></p>
                </div>
                <div class="profile_field_input">
                    <input class="widefat" type="text" name="field_default_value" id="field_default_value" value="<?php echo esc_attr(get_post_meta($post->ID, '__field_default_value', true)); ?>" />                        
                </div>
            </div>
		<?php
    }

    if ('text' == $type || 'text_url' == $type) {
        ?>			
            <div class="profile_field_meta_box clearfix">
                <div class="profile_field_label">
                    <label for="field_placeholder"><?php _e('Placeholder', 'profile'); ?></label>
                </div>
                <div class="profile_field_input">
                    <input class="widefat" type="text" name="field_placeholder" id="field_placeholder" value="<?php echo esc_attr(get_post_meta($post->ID, '__field_placeholder', true)); ?>" />                        
                </div>
            </div>
		<?php
    }

    if ('select' == $type || 'radio' == $type) {
        $field_options = get_post_meta($post->ID, '__field_options', true);
        ?>			
            <div class="profile_field_meta_box clearfix">
                <div class="profile_field_label">
                    <label for="field_options"><?php _e('Options', 'profile'); ?></label>
                </div>
                <div class="profile_field_input">
                	<label>
                		<input data-action="toggle" data-show="#field_options_callback" data-hide="#field_options_repeats" type="checkbox" name="field_options[is_callback]" id="field_options" value="1"<?php echo isset($field_options['is_callback']) && (bool) @$field_options['is_callback'] ? ' checked="checked"' : ''  ?> />
                		<?php _e('Get option from callback ?', 'profile') ?>
                	</label>
                	<div id="field_options_callback"<?php echo isset($field_options['is_callback']) && (bool) @$field_options['is_callback'] ? '' : ' style="display:none"' ?>>
                		<input class="widefat" type="text" value="" name="field_options[callback]" placeholder="<?php _e('callback function name', 'profile') ?>" />
                	</div>
                	<div id="field_options_repeats" data-cont="repeat_fields" <?php echo isset($field_options['is_callback']) || (bool) @$field_options['is_callback'] ? ' style="display:none"' : '' ?>>

                    	<div id="field_options_##" class="profile_repeat_field" data-cont="default_repat_field" style="display:none">
                    		<input type="text" value="" name="key" placeholder="<?php _e('Key', 'profile') ?>" />
                    		<input type="text" value="" name="value" placeholder="<?php _e('Value', 'profile') ?>" />
                    		<a href="#" data-action="remove_repeat_field" class="btn-profile_remove_repat profileicon-cross" title="<?php _e('Remove', 'profile') ?>"></a>
                    	</div>
                    	<?php 
                            if (is_array($field_options)) {
                                                        foreach ($field_options as $k => $fields) { 
                                if ($k !== 'callback' && $k !== 'is_callback') {
                                    ?>
									<div id="field_options_<?php echo $k ?>" class="profile_repeat_field" data-cont="default_repat_field">
			                    		<input type="text" value="<?php echo $fields['key'] ?>" name="field_options[<?php echo $k ?>][key]" placeholder="<?php _e('Key', 'profile') ?>" />
			                    		<input type="text" value="<?php echo $fields['value'] ?>" name="field_options[<?php echo $k ?>][value]" placeholder="<?php _e('Value', 'profile') ?>" />
			                    		<a href="#" data-action="remove_repeat_field" class="btn-profile_remove_repat profileicon-cross" title="<?php _e('Remove', 'profile') ?>"></a>
			                    	</div>
		                    	<?php 
                                }
                            
                            } ?>
                    	<a href="#" data-action="add_repeat_field" class="btn-profile_add_repat profileicon-plus"><?php _e('Add an option', 'profile') ?></a>
                    </div>                        
                </div>
            </div>
		<?php
    }

    ?>
		<div class="profile_field_meta_box clearfix">
	        <div class="profile_field_label">
	            <label for="field_input_class"><?php _e('Input class', 'profile');
                            }
                            ?></label>
	        </div>
	        <div class="profile_field_input">
	        	<label>
	            	<input type="text" name="field_input_class" id="field_input_class" value="<?php echo esc_attr(get_post_meta($post->ID, '__field_input_class', true)); ?>" />
	            	<?php _e('CSS class for input field.', 'profile') ?>
	            </label>                        
	        </div>
	    </div>
	<?php

    do_action('profile_field_option_by_type', $type, $post);

    echo '</div>';
}