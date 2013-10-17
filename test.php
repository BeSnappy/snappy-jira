<?php

require 'vendor/autoload.php';

function head($array)
{
	return reset($array);
}

$app = new Snappy\Apps\Jira\App;
$app->setConfig(array(
	'project' => 'SNAP',
	'username' => '',
	'password' => '',
	'tag' => '#jira',
	'url' => 'https://userscape.atlassian.net',
	'type' => 'New Feature',
));

$app->handleTagsChanged(array('default_subject' => 'Foo', 'id' => 1, 'notes' => array(array('content' => 'Bar'))), array('#jira'), array());
