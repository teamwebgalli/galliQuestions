<?php

$answer_guid = (int) get_input('guid');
$answer = get_entity($answer_guid);

$question = $answer->getContainerEntity();
$question_guid = $question->guid;

$todo = get_input('todo');

if($answer && $question && $todo){
	if($todo == 'mark'){
		if(add_entity_relationship($answer_guid, 'is_answer_of', $question_guid)){
			system_message(elgg_echo('questions:marksticky:success'));
			forward(REFERER);
		}
	}
	if($todo == 'unmark'){
		if(remove_entity_relationship($answer_guid, 'is_answer_of', $question_guid)){
			system_message(elgg_echo('questions:unmarksticky:success'));
			forward(REFERER);
		}
	}
}

register_error(elgg_echo('error:default'));
forward(REFERER);