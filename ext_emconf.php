<?php
$EM_CONF[$_EXTKEY] = array (
  'title' => 'TypoScript Rendering',
  'description' => 'Can render a TypoScript path by URL, especially useful for Ajax dispatching',
  'category' => 'Rendering',
  'author' => 'Helmut Hummel',
  'author_email' => 'info@helmut-hummel.de',
  'author_company' => 'helhum.io',
  'state' => 'stable',
  'uploadfolder' => '0',
  'createDirs' => '',
  'clearCacheOnLoad' => 0,
  'version' => '3.0.0',
  'constraints' =>
  array (
    'depends' =>
    array (
      'php' => '7.0.0-7.3.999',
      'typo3' => '8.7.0-9.5.99',
    ),
    'conflicts' =>
    array (
    ),
    'suggests' =>
    array (
    ),
  ),
);
