<?php
elgg_register_event_handler('init', 'system', 'questions_init');

function questions_init() {
	$root = dirname(__FILE__);
	elgg_register_library('elgg:questions', "$root/lib/questions.php");
	
	// actions
	$action_path = "$root/actions/questions";
	elgg_register_action('questions/save', "$action_path/save.php");
	elgg_register_action('questions/delete', "$action_path/delete.php");
	elgg_register_action('questions/mark', "$action_path/mark.php");
	
	// menus
	elgg_register_menu_item('site', array(
		'name' => 'questions',
		'text' => elgg_echo('questions'),
		'href' => 'questions/all'
	));
	
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'questions_owner_block_menu');
	elgg_register_page_handler('questions', 'questions_page_handler');
	
	elgg_extend_view('css/elgg', 'questions/css');
	elgg_extend_view('js/elgg', 'questions/js');
	
	elgg_register_widget_type('questions', elgg_echo('questions'), elgg_echo('questions:widget:description'));

	// Register for notifications
	elgg_register_notification_event('object', 'questions', array('create'));
	elgg_register_plugin_hook_handler('prepare', 'notification:create:object:questions', 'questions_prepare_notification');
	
	elgg_register_entity_url_handler('object', 'questions', 'questions_url');
	
	elgg_register_entity_type('object', 'questions');
	
	add_group_tool_option('questions', elgg_echo('questions:enablequestions'), true);
	elgg_extend_view('groups/tool_latest', 'questions/group_module');
		
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'questions_answers_entity_menu_setup');	
}
	
function questions_page_handler($page) {
	elgg_load_library('elgg:questions');
	if (!isset($page[0])) {
		$page[0] = 'all';
	}
	elgg_push_breadcrumb(elgg_echo('questions'), 'questions/all');
	$pages = dirname(__FILE__) . '/pages/questions';
	switch ($page[0]) {
		case "all":
			include "$pages/all.php";
			break;
		case "owner":
			include "$pages/owner.php";
			break;
		case "friends":
			include "$pages/friends.php";
			break;
		case "view":
			set_input('guid', $page[1]);
			include "$pages/view.php";
			break;
		case "add":
			gatekeeper();
			include "$pages/add.php";
			break;
		case "edit":
			gatekeeper();
			set_input('guid', $page[1]);
			include "$pages/edit.php";
			break;
		case 'group':
			group_gatekeeper();
			include "$pages/owner.php";
			break;
		default:
			return false;
	}
	elgg_pop_context();
	return true;
}

function questions_url($entity) {
	global $CONFIG;
	$title = $entity->title;
	$title = elgg_get_friendly_title($title);
	return $CONFIG->url . "questions/view/" . $entity->getGUID() . "/" . $title;
}

function questions_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'user')) {
		$url = "questions/owner/{$params['entity']->username}";
		$item = new ElggMenuItem('questions', elgg_echo('questions'), $url);
		$return[] = $item;
	} else {
		if ($params['entity']->questions_enable != 'no') {
			$url = "questions/group/{$params['entity']->guid}/all";
			$item = new ElggMenuItem('questions', elgg_echo('questions:group'), $url);
			$return[] = $item;
		}
	}

	return $return;
}

function questions_prepare_notification($hook, $type, $notification, $params) {
	$entity = $params['event']->getObject();
	$owner = $params['event']->getActor();
	$recipient = $params['recipient'];
	$language = $params['language'];
	$method = $params['method'];

	$descr = $entity->description;
	$title = $entity->title;

	$notification->subject = elgg_echo('questions:notify:subject', array($title), $language); 
	$notification->body = elgg_echo('questions:notify:body', array(
		$owner->name,
		$title,
		$descr,
		$entity->getURL()
	), $language);
	$notification->summary = elgg_echo('questions:notify:summary', array($entity->title), $language);

	return $notification;
}

function questions_profile_fields(){
	return array(
		array( 'name' => 'title',			'type' => 'text',		'value' => ''),
		array( 'name' => 'description', 	'type' => 'longtext', 	'value' => ''),
	);
}

function questions_answers_entity_menu_setup($hook, $type, $return, $params) {
	$answer = $params['entity'];
	$answer_guid = $answer->guid;
	
	$question = $answer->getContainerEntity();
	$question_guid = $question->guid;
		
	// Only for questions
	if($question){
		$subtype = $question->getSubtype();
		if ($subtype != 'questions') {
			return $return;
		}
	} else {
		return $return;
	}
	
	// Not in widgets
	if (elgg_in_context('widgets')) {
		return $return;
	}
	
	if($question->canEdit()){
		// Already marked this post as the answer?
		if(check_entity_relationship($answer_guid, 'is_answer_of', $question_guid)){
			$text = elgg_echo('questions:unmark');
			$href = "action/questions/mark?todo=unmark&guid=$answer_guid";
		} else {
			$answer = elgg_get_entities_from_relationship(array('type' => 'object', 'relationship' => 'is_answer_of', 'relationship_guid' => $question->guid, 'inverse_relationship' => TRUE));
			if(!$answer){		
				$text = elgg_echo('questions:mark');
				$href = "action/questions/mark?todo=mark&guid=$answer_guid";
			}
		}	
		if($text & $href){
			$options = array(
				'name' => 'mark-answers',
				'text' => $text,
				'href' => $href,
				'class' => $class,
				'priority' => 150,
				'is_action' => true,
			);
			$return[] = ElggMenuItem::factory($options);
		}
	}
	
	return $return;
}		
