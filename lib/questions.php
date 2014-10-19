<?php
function questions_prepare_form_vars($questions = null) {
	$fields = questions_profile_fields();
	$values = array(
		'access_id' => ACCESS_DEFAULT,
		'container_guid' => elgg_get_page_owner_guid(),
		'guid' => null,
		'entity' => $questions,
	);
	if ($fields) {
		foreach ($fields as $name => $type) {
			$values[$name] = '';
		}
	}	
	if ($questions) {
		if ($fields) {
			foreach ($fields as $field) {
				$fname = $field['name'];
				$values[$fname] = $questions->$fname;
			}
		}	
		foreach (array_keys($values) as $field) {
			if (isset($questions->$field)) {
				$values[$field] = $questions->$field;
			}
		}
	}
	if (elgg_is_sticky_form('questions')) {
		$sticky_values = elgg_get_sticky_values('questions');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}
	elgg_clear_sticky_form('questions');
	return $values;
}