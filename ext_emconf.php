<?php
$EM_CONF[$_EXTKEY] = array(
	'title' => 'TypoScript Rendering',
	'description' => 'Can render a TypoScript snippet by URL, especially useful for Ajax dispatching',
	'category' => 'Rendering',
	'author' => 'Helmut Hummel',
	'author_email' => 'info@helmut-hummel.de',
	'author_company' => 'Helmut Hummel',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '1.0.1',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.7-5.5.999',
			'typo3' => '6.0.0-6.2.999',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

?>