<?php
/**
 * Profile field type Text form template
 */
$value = get_user_meta($user_id, '__profile_field_'.$field->ID, true);
$value = $value ? $value : $options->__field_default_value;
?>
<div class="meta-field">
	<span class="meta-fields-label"><?php echo $field->post_title ?></span>
	<div class="meta-values">
	
		<?php if (profile_user_can_edit_field(get_current_user_id())): ?>
			<a class="btn-edit-profile-field" href="#" data-action="edit_profile_field" data-query="field=<?php echo $field->ID ?>&profile_ajax_action=edit_profile_field"><?php _e('Edit', 'profile') ?></a>
		<?php endif; ?>
		
		<div class="user-field-form">
			<span class="meta-field-value"><a href="<?php echo $value ?>" rel="nofollow"><?php echo $value ?></a></span>
		</div>
	</div>
</div>