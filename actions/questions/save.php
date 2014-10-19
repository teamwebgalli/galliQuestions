<?php

$title = get_input('title');
$description = get_input('description');
$access_id = get_input('access_id');
$tags = get_input('tags');
$guid = get_input('guid');
$container_guid = get_input('container_guid', elgg_get_logged_in_user_guid());

elgg_make_sticky_form('questions');

if (!$title || !$description) {
	register_error(elgg_echo('questions:save:failed'));
	forward(REFERER);
}

if ($guid == 0) {
	$questions = new ElggObject;
	$questions->subtype = "questions";
	$questions->container_guid = (int)get_input('container_guid', elgg_get_logged_in_user_guid());
	$new = true;
} else {
	$questions = get_entity($guid);
	if (!$questions->canEdit()) {
		system_message(elgg_echo('questions:save:failed'));
		forward(REFERRER);
	}
}

$fields = questions_profile_fields();
if ($fields) {
	foreach ($fields as $field) {
		$name = $field['name'];
		$value = get_input($name);
		if($name == 'tags'){
			$value = string_to_tag_array($value);
		}
		if($value){
			$questions->$name = $value;
		}
	}	
}
$questions->access_id = $access_id;

if ($questions->save()) {	
	elgg_clear_sticky_form('questions');
	system_message(elgg_echo('questions:save:success'));
	//add to river only if new
	if ($new) {
		elgg_create_river_item(array(
			'view' => 'river/object/questions/create',
			'action_type' => 'create',
			'subject_guid' => elgg_get_logged_in_user_guid(),
			'object_guid' => $questions->getGUID(),
		));
	}
	forward($questions->getURL());
} else {
	register_error(elgg_echo('questions:save:failed'));
	forward("questions/all/");
}