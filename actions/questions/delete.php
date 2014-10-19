<?php

$guid = get_input('guid');
$questions = get_entity($guid);

if (elgg_instanceof($questions, 'object', 'questions') && $questions->canEdit()) {
	$container = $questions->getContainerEntity();
	$owner_guid = $questions->owner_guid;
	if ($questions->delete()) {
		system_message(elgg_echo("questions:delete:success"));
		forward($container->getURL());
	}
}

register_error(elgg_echo("questions:delete:failed"));
forward(REFERER);
