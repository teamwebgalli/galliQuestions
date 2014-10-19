<?php
$access_id = elgg_extract('access_id', $vars, ACCESS_DEFAULT);
$container_guid = elgg_extract('container_guid', $vars);
$guid = elgg_extract('guid', $vars, null);

$fields = questions_profile_fields();
if ($fields) {
	foreach ($fields as $field) {
		$name = $field['name'];
		$type = $field['type'];		
		$options_values = $field['options_values'];
		$value = elgg_extract($name, $vars, '');
?>
		<div>
			<label><?php echo elgg_echo("questions:$name"); ?></label><br />
			<?php echo elgg_view("input/$type", array('name' => $name, 'value' => $value , 'options_values' => $options_values)); ?>
		</div>
<?php
	}
}
?>
	
<div>
	<label><?php echo elgg_echo('access'); ?></label><br />
	<?php echo elgg_view('input/access', array('name' => 'access_id', 'value' => $access_id)); ?>
</div>

<div class="elgg-foot">
<?php
echo elgg_view('input/hidden', array('name' => 'container_guid', 'value' => $container_guid));
if ($guid) {
	echo elgg_view('input/hidden', array('name' => 'guid', 'value' => $guid));
}
echo elgg_view('input/submit', array('value' => elgg_echo("save")));
?>
</div>