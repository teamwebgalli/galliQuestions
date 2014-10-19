<?php

$questions_guid = get_input('guid');
$questions = get_entity($questions_guid);

if (!elgg_instanceof($questions, 'object', 'questions') || !$questions->canEdit()) {
	register_error(elgg_echo('questions:unknown_questions'));
	forward(REFERRER);
}

$page_owner = elgg_get_page_owner_entity();

$title = elgg_echo('questions:edit');
elgg_push_breadcrumb($title);

// $form_vars = array('enctype' => 'multipart/form-data');
$vars = questions_prepare_form_vars($questions);
$content = elgg_view_form('questions/save', array(), $vars);

$body = elgg_view_layout('content', array(
	'filter' => '',
	'content' => $content,
	'title' => $title,
));

echo elgg_view_page($title, $body);