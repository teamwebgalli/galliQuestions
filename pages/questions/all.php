<?php

elgg_pop_breadcrumb();
elgg_push_breadcrumb(elgg_echo('questions'));

elgg_register_title_button();

$content = elgg_list_entities(array(
	'type' => 'object',
	'subtype' => 'questions',
	'limit' => 10,
	'full_view' => false,
	'view_toggle_type' => false,
	'no_results' => elgg_echo('questions:none'),
));

$title = elgg_echo('questions:everyone');

$body = elgg_view_layout('content', array(
	'filter_context' => 'all',
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('questions/sidebar'),
));

echo elgg_view_page($title, $body);